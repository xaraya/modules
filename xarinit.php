<?php
/**
 * Dynamic Data Example Module - documented module template
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage dyn_example
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool True on succes of init
 */
function dyn_example_init()
{
    /**
     * import the object definition and properties from some XML file (exported from DD)
     */

# --------------------------------------------------------
#
# Create DD objects
#
# The object XML files located in the xardata folder of the module.
# The file names have the form e.g.
#     dyn_example-def.xml
#     dyn_example-dat.xml
#
# The first is a definition file for the object, and needs to be present if you list dyn_example
# among the objects to be created in the array below. The actual object name nneds to correspond
# to the first part of the definition file name, e.g. dyn_example.
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
    $module = 'dyn_example';
    $objects = array(
            'dyn_example',
            'modulesettings',
//            'usersettings',
            );

    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;
    
# --------------------------------------------------------
#
# Set up modvars
#
    xarModVars::set('dyn_example','bold',false);
    xarModVars::set('dyn_example','itemsperpage',20);

# --------------------------------------------------------
#
# Register blocks
#
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'dyn_example',
                             'blockType' => 'first'))) return;
# --------------------------------------------------------
#
# Create privilege instances
#
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'dyn_example'));
    $objectid = $object->objectid;

    $xartable =& xarDB::getTables();
    $dynproptable = $xartable['dynamic_properties'];
    $dyndatatable = $xartable['dynamic_data'];
    $query = "SELECT DISTINCT xar_dd_itemid
                FROM $dynproptable
           LEFT JOIN $dyndatatable
                  ON xar_prop_id=xar_dd_propid
               WHERE xar_prop_objectid= $objectid";

    // Note : we could add some other fields in here too, based on the properties we imported above
    $instances = array(
                        array('header' => 'Dynamic Example ID:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('dyn_example', 'Item', $instances);

# --------------------------------------------------------
#
# Register masks
#
    xarRegisterMask('ViewDynExample','All','dyn_example','Item','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadDynExample','All','dyn_example','Item','All','ACCESS_READ');
    xarRegisterMask('EditDynExample','All','dyn_example','Item','All','ACCESS_EDIT');
    xarRegisterMask('AddDynExample','All','dyn_example','Item','All','ACCESS_ADD');
    xarRegisterMask('DeleteDynExample','All','dyn_example','Item','All','ACCESS_DELETE');
    xarRegisterMask('AdminDynExample','All','dyn_example','Item','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times
 */
function dyn_example_upgrade($oldversion)
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
function dyn_example_delete()
{
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'dyn_example',
                             'blockType' => 'first'))) return;

# --------------------------------------------------------
#
# Uninstall the module
#
# The function below pretty much takes care of everything that needs to be removed
#
    $module = 'dyn_example';
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>