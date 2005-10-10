<?php
/**
 * Lists initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage lists
 */

/**
 * initialise the lists module
 *
 * @Original Author: Jason Judge
 * @author Lists module development team 
 */
function lists_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_lists = $xartable['lists_types'];
    $table_items = $xartable['lists_items'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields_lists = "
        xar_tid             I           AUTO    PRIMARY,
        xar_list_type_id    I           Null,
        xar_type            C(1)        NOTNULL,
        xar_name            C(100)      NotNull DEFAULT 'name',
        xar_desc            X(2000)     Null    DEFAULT '',
        xar_order_columns   C(200)      Null    DEFAULT ''
    ";

    $fields_items = "
        xar_iid             I           AUTO    PRIMARY,
        xar_lid             I           NotNull,
        xar_code            C(100)      NotNull DEFAULT 'code',
        xar_short_name      C(100)      NotNull DEFAULT 'short_name',
        xar_long_name       C(200)      Null,
        xar_desc            X(2000)     Null    DEFAULT '',
        xar_order           I           Null
    ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($table_lists, $fields_lists);
    if (!$result) {return;}

    // Create a unique key on the name column.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_lists_name',
        $table_lists,
        'xar_name'
    );

    if (!$result) {return;}
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($table_items, $fields_items);
    if (!$result) {return;}

    // Create an index on the code column.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_items_code',
        $table_items,
        'xar_code'
    );
    if (!$result) {return;}

    // Create a unique index on the lid and code columns.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_u1',
        $table_items,
        array('xar_lid', 'xar_code')
    );
    if (!$result) {return;}

    // Configure module variables.
    //xarModSetVar('example', 'bold', 0);

    // Register our hooks that we are providing to other modules.  The example
    // module shows an example hook in the form of the user menu.
/*
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'example', 'user', 'usermenu')) {
        return false;
    }
*/
    /**
     * Define instances for this module
     * Format is
     * setInstance(Module,Type,ModuleTable,IDField,NameField,ApplicationVar,LevelTable,ChildIDField,ParentIDField)
     */

/*
    $query1 = "SELECT DISTINCT xar_name FROM " . $exampletable;
    $query2 = "SELECT DISTINCT xar_number FROM " . $exampletable;
    $query3 = "SELECT DISTINCT xar_exid FROM " . $exampletable;
    $instances = array(
        array('header' => 'Example Name:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Example Number:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Example ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
*/
    //xarDefineInstance('example', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    //xarRegisterMask('ReadExampleBlock', 'All', 'example', 'Block', 'All', 'ACCESS_OVERVIEW');

    // Initialisation successful.
    return true;
}

/**
 * Upgrade the module.
 */
function lists_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '0.1.0':
    }

    // Upgrade successful.
    return true;
}

/**
 * delete the lists module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function lists_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    $table_lists = $xartable['lists_types'];
    $table_items = $xartable['lists_items'];
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the lists tables */
     $result = $datadict->dropTable($table_lists);
     $result = $datadict->dropTable($table_items);
     
    // Deletion successful
    return true;
}

?>