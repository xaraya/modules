<?php
/**
 * Contact Form Module - documented module template
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage contactform
 * @link http://xaraya.com/index.php/release/1049.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool True on succes of init
 */
function contactform_init()
{
# --------------------------------------------------------
#
# Create DD objects
#
# The object XML files located in the xardata folder of the module.
# The file names have the form e.g.
#     contactform-def.xml
#     contactform-dat.xml
#
# The first is a definition file for the object, and needs to be present if you list contactform
# among the objects to be created in the array below.
#
# The second is a defintion file for the object's items, i.e. its data. This file can be omitted.
#
# You can create these files manually, for example by cutting and pasting from an existing example.
# The easier way is to create an object (and perhaps its items) using the user interface of the
# DynamicData module. Once you have an object (and items), you can export it into an XML file using the
# DD module's export facility.
#
# Note: the object(s) created below are automatically kept track of so that the module knows to remove them when
# you deinstall it.
#
    $module = 'contactform';
    $objects = array(
                'contactform',
                'contactform_module_settings',
                'contactform_user_settings',
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;
# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
# Since this modvar is used as storage in a DD object contactform_module_settings,
# we could also let Xaraya define it, but that would mean we wouldn't have it until
# we updated the modifyconfig page
#
    xarModVars::set('contactform','to_email','');
    xarModVars::set('contactform','default_subject','New Message from My Site');
	xarModVars::set('contactform','save_to_db',true);
	xarModVars::set('contactform','enable_short_urls',true);

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#
# The common settings use the module_settings dataobject. which is created when Xaraya is installed
# These next lines initialize the appropriate modvars that object uses for contactform, if they don't already exist.
# The lines below corresponding to the initializeation of the core modules are found in modules/installer/xaradmin.php.
# The module_settings dataobject itself is defined in the dynamicdata module.
#
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'contactform'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
  /*  if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'contactform',
                             'blockType' => 'first'))) return;*/
# --------------------------------------------------------
#
# Create privilege instances
#
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'contactform'));
    $objectid = $object->objectid;

    $xartable =& xarDB::getTables();
    $dynproptable = $xartable['dynamic_properties'];
    $dyndatatable = $xartable['dynamic_data'];
    $query = "SELECT DISTINCT $dynproptable.id
	FROM $dynproptable
	LEFT JOIN $dyndatatable
                  ON $dyndatatable.id=property_id
               WHERE object_id= $objectid";

    // Note : we could add some other fields in here too, based on the properties we imported above
    $instances = array(
                        array('header' => 'Contact Form ID:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('contactform', 'Item', $instances);
# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewContactForm','All','contactform','Item','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadContactForm','All','contactform','Item','All','ACCESS_READ');
    xarRegisterMask('EditContactForm','All','contactform','Item','All','ACCESS_EDIT');
    xarRegisterMask('AddContactForm','All','contactform','Item','All','ACCESS_ADD');
    xarRegisterMask('DeleteContactForm','All','contactform','Item','All','ACCESS_DELETE');
    xarRegisterMask('AdminContactForm','All','contactform','Item','All','ACCESS_ADMIN');
# --------------------------------------------------------
#
# Register hooks
#

    // Initialisation successful
    return true;
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times
 */
function contactform_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success of deletion
 */
function contactform_delete()
{
    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'contactform',
                             'blockType' => 'first'))) return;

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything that needs to be removed
#
    $module = 'contactform';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>