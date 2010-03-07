<?php
/**
 * fedexws
 *
 * @package modules
 * @copyright (C) 2009 WebCommunicate.net
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage fedexws
 * @link http://xaraya.com/index.php/release/1032.html
 * @author Ryan Walker <ryan@webcommunicate.net>
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool True on succes of init
 */
function fedexws_init()
{
# --------------------------------------------------------
#
# Create DD objects
#
# The object XML files located in the xardata folder of the module.
# The file names have the form e.g.
#     fedexws-def.xml
#     fedexws-dat.xml
#
# The first is a definition file for the object, and needs to be present if you list fedexws
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
    $module = 'fedexws';
    $objects = array(
                'fedexws_rate',
				'fedexws_module_settings',
				'fedexws_user_settings'
                );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;    
# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#
# Since this modvar is used as storage in a DD object fedexws_module_settings,
# we could also let Xaraya define it, but that would mean we wouldn't have it until
# we updated the modifyconfig page
#
    xarModVars::set('fedexws','key','');
	xarModVars::set('fedexws','password','');
	xarModVars::set('fedexws','acctnumber','');
	xarModVars::set('fedexws','meternumber','');

# --------------------------------------------------------
#
# Set up configuration modvars (general)
#
# The common settings use the module_settings dataobject. which is created when Xaraya is installed
# These next lines initialize the appropriate modvars that object uses for fedexws, if they don't already exist.
# The lines below corresponding to the initializeation of the core modules are found in modules/installer/xaradmin.php.
# The module_settings dataobject itself is defined in the dynamicdata module.
#
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'fedexws'));
        $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
/*    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'fedexws',
                             'blockType' => 'first'))) return;*/
# --------------------------------------------------------
#
# Create privilege instances
#
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'fedexws_rate'));
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
                        array('header' => 'fedexws ID:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('fedexws', 'Item', $instances);

	xarRemoveMasks('fedexws');

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewFedExWS','All','fedexws','Item','All:All:All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadFedExWS','All','fedexws','Item','All:All:All','ACCESS_READ');
    xarRegisterMask('EditFedExWS','All','fedexws','Item','All:All:All','ACCESS_EDIT');
    xarRegisterMask('AddFedExWS','All','fedexws','Item','All:All:All','ACCESS_ADD');
    xarRegisterMask('DeleteFedExWS','All','fedexws','Item','All:All:All','ACCESS_DELETE');
    xarRegisterMask('AdminFedExWS','All','fedexws','Item','All:All:All','ACCESS_ADMIN');
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
function fedexws_upgrade($oldversion)
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
function fedexws_delete()
{
    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'fedexws',
                             'blockType' => 'first'))) return;

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything that needs to be removed
#
    $module = 'fedexws';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>