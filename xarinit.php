<?php
/**
 * Dynamic Data Example Module - documented module template
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * initialise the dyn_example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function dyn_example_init()
{
    // this module can't work without the dynamicdata module
    $testmod = xarModIsAvailable('dynamicdata');
    if (!isset($testmod)) return; // some other exception got in our way [locale for instance :)]

    if (!$testmod) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    /**
     * import the object definition and properties from some XML file (exported from DD)
     */

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/dyn_example/dyn_example.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('dyn_example','objectid',$objectid);

    /**
     * or do it the hard way, and create everything step by step
     */

/*
 * start doing it the hard way *

    // 1. create the dynamic object that will represent our items
    $objectid = xarModAPIFunc('dynamicdata','admin','createobject',
                              array('name'     => 'dyn_example',                      // some unique object name
                                    'label'    => 'Dynamic Example',                 // label to use for display
                                    'moduleid' => xarModGetIDFromName('dyn_example'), // this module
                                    'itemtype' => 0,                                 // we only handle 1 item type here (for now)
                                    'urlparam' => 'itemid',                          // the default URL parameter
                                    'config'   => 'nothing yet',                     // some configuration you might want to specify
                                    'maxid'    => 0,                                 // the highest item id up to now
                                    'isalias'  => 1));                               // use this as an alias in short URLs
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('dyn_example','objectid',$objectid);

    // 2. assign some properties to this object

    // 2.a. we always need one property that will hold the unique item id
    $propertyid = xarModAPIFunc('dynamicdata','admin','createproperty',
                                array('name'       => 'id',                              // some unique property name (for this object)
                                      'label'      => 'Id',                              // label to use for display
                                      'objectid'   => $objectid,                         // see above
                                      'moduleid'   => xarModGetIDFromName('dyn_example'), // see above
                                      'itemtype'   => 0,                                 // see above
                                      'type'       => 21,                                // Item ID
                                      'default'    => 0,                                 // some default value
                                      'source'     => 'dynamic_data',                    // in this case, we'll put everything in dynamic_data
                                      'status'     => 1,                                 // this property will be shown in lists/views too
                                      'order'      => 1,                                 // it's going to be field #1 in lists/views and forms/displays
                                      'validation' => ''));                              // there is no specific validation rule for this
    if (empty($propertyid)) return;

    // 2.b. some more properties go here...
    $propertyid = xarModAPIFunc('dynamicdata','admin','createproperty',
                                array('name'       => 'name',
                                      'label'      => 'Name',
                                      'objectid'   => $objectid,
                                      'moduleid'   => xarModGetIDFromName('dyn_example'),
                                      'itemtype'   => 0,
                                      'type'       => 2,                                 // Text Box
                                      'default'    => 'your name',                       // some default value
                                      'source'     => 'dynamic_data',
                                      'status'     => 1,
                                      'order'      => 2,                                 // it's going to be field #2 in lists/views and forms/displays
                                      'validation' => '1:30'));                          // min. 1 character, max. 30 characters
    if (empty($propertyid)) return;

    $propertyid = xarModAPIFunc('dynamicdata','admin','createproperty',
                                array('name'       => 'age',
                                      'label'      => 'Age',
                                      'objectid'   => $objectid,
                                      'moduleid'   => xarModGetIDFromName('dyn_example'),
                                      'itemtype'   => 0,
                                      'type'       => 15,                                // Number Box
                                      'default'    => '',
                                      'source'     => 'dynamic_data',
                                      'status'     => 1,
                                      'order'      => 3,                                 // it's going to be field #3 in lists/views and forms/displays
                                      'validation' => '0:125'));                         // min. value 0, max. value 125
    if (empty($propertyid)) return;

    $propertyid = xarModAPIFunc('dynamicdata','admin','createproperty',
                                array('name'       => 'picture',
                                      'label'      => 'Picture',
                                      'objectid'   => $objectid,
                                      'moduleid'   => xarModGetIDFromName('dyn_example'),
                                      'itemtype'   => 0,
                                      'type'       => 12,                                // Image
                                      'default'    => '',
                                      'source'     => 'dynamic_data',
                                      'status'     => 2,                                 // only show this property in forms and displays, not in lists and views
                                      'order'      => 4,                                 // it's going to be field #4 in those forms/displays
                                      'validation' => ''));
    if (empty($propertyid)) return;

 * stop doing it the hard way *
 */

    /**
     * import some initial data from some XML file (exported from DD)
     */

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/dyn_example/dyn_example.data.xml'));
    if (empty($objectid)) return;

    /**
     * or do it the hard way, and create the items here as well
     */

/*
 * start doing it the hard way *

    // 3. add some sample items here if you want to (or import them too)

    $itemid = xarModAPIFunc('dynamicdata','admin','create',
                            array('modid'    => xarModGetIDFromName('dyn_example'), // see above
                                  'itemtype' => 0,                                 // see above
                                  'itemid'   => 0,                                 // we don't know the item id here yet - it will be assigned by DD
                                  'values'   => array(                             // here you specify the value for the different properties, by name
                                                    'name'    => 'Johnny',         // Note : the property defined as item id (= 'id' here) will be filled in automatically
                                                    'age'     => 32,
                                                    'picture' => 'http://mikespub.net/xaraya/images/cuernos1.jpg'
                                                     )
                                 )
                           );
    if (empty($itemid)) return;

    // and so on...

 * stop doing it the hard way *
 */

    xarModSetVar('dyn_example','bold',0);
    xarModSetVar('dyn_example','itemsperpage',20);

    /**
     * import dynamic module and user settings from some XML file (optional)
     */

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/dyn_example/modulesettings.xml'));
    if (empty($objectid)) return;
    xarModSetVar('dyn_example','modulesettings',$objectid);

    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/dyn_example/usersettings.xml'));
    if (empty($objectid)) return;
    xarModSetVar('dyn_example','usersettings',$objectid);

    $xartable =& xarDBGetTables();

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'dyn_example',
                             'blockType' => 'first'))) return;

/*
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'dyn_example'";
    $instances = array(
                        array('header' => 'Dynamic Example Block Title:',
                                'query' => $query,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('dyn_example','Block',$instances);

    xarRegisterMask('ReadDynExampleBlock','All','dyn_example','Block','All','ACCESS_OVERVIEW');
*/

    $objectid = xarModGetVar('dyn_example','objectid');
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
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function dyn_example_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.5':
            // Version 0.5 didn't have a 'picture' field, it was added
            // in version 1.0

            // $objectid = xarModGetVar('dyn_example','objectid');

            // 1. suppose we forgot which object id we were using, so we'll look it up
            $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                        array('modid'    => xarModGetIDFromName('dyn_example'), // it's this module
                                              'itemtype' => 0));                               // with no item type
            if (!isset($objectinfo) || empty($objectinfo['objectid'])) {
               // if we can't find the object, it was probably removed by hand -> bail out
               return;
            }
            $objectid = $objectinfo['objectid'];

            // 2. add the missing property now
            $propertyid = xarModAPIFunc('dynamicdata','admin','createproperty',
                                array('name'       => 'picture',
                                      'label'      => 'Picture',
                                      'objectid'   => $objectid,
                                      'moduleid'   => xarModGetIDFromName('dyn_example'),
                                      'itemtype'   => 0,
                                      'type'       => 12,                                // Image
                                      'default'    => '',
                                      'source'     => 'dynamic_data',
                                      'status'     => 2,                                 // only show this property in forms and displays, not in lists and views
                                      'order'      => 4,                                 // it's going to be field #4 in those forms/displays
                                      'validation' => ''));
            if (empty($propertyid)) return;

            // At the end of the successful completion of this function we
            // fall through to the next upgrade

        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here

            // Register blocks
            if (!xarModAPIFunc('blocks',
                               'admin',
                               'register_block_type',
                               array('modName' => 'dyn_example',
                                     'blockType' => 'first'))) return;

            // At the end of the successful completion of this function we
            // fall through to the next upgrade

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the dyn_example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function dyn_example_delete()
{

    // delete the dynamic objects and their properties

    $objectid = xarModGetVar('dyn_example','objectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    xarModDelVar('dyn_example','objectid');

    $objectid = xarModGetVar('dyn_example','modulesettings');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    xarModDelVar('dyn_example','modulesettings');

    $objectid = xarModGetVar('dyn_example','usersettings');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    xarModDelVar('dyn_example','usersettings');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName' => 'dyn_example',
                             'blockType' => 'first'))) return;

    // Remove Masks and Instances
    xarRemoveMasks('dyn_example');
    xarRemoveInstances('dyn_example');

    // Deletion successful
    return true;
}

?>
