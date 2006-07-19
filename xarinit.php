<?php
/**
 * Lists initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Initialise the lists module
 *
 * Original Author: Jason Judge
 * @author Lists module development team
 */
function lists_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $table_types = $xartable['lists_types'];
    $table_items = $xartable['lists_items'];

    $fields = array(
        'xar_tid'               =>array('null'=>false, 'type'=>'integer','unsigned'=>true,  'increment' => true, 'primary_key' => true),
        'xar_list_type_id'      =>array('null'=>true, 'type'=>'integer'),
        'xar_type'              =>array('null'=>false, 'type'=>'char','size'=>1),
        'xar_name'              =>array('null'=>false, 'type'=>'char','size'=>100, 'default'=>'name'),
        'xar_desc'              =>array('null'=>true, 'type'=>'text','size'=>'large', 'default'=>''),
        'xar_order_columns'     =>array('null'=>true,  'type'=>'char','size'=> 200,'default'=>'')
    );
    $query = xarDBCreateTable($table_types, $fields);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $fields = array(
        'xar_iid'               =>array('null'=>false, 'type'=>'integer','unsigned'=>true,  'increment' => true, 'primary_key' => true),
        'xar_lid'               =>array('null'=>false, 'type'=>'integer'),
        'xar_code'              =>array('null'=>false, 'type'=>'char','size'=>100, 'default'=>'code'),
        'xar_short_name'        =>array('null'=>false, 'type'=>'char','size'=>100, 'default'=>'short_name'),
        'xar_long_name'         =>array('null'=>true, 'type'=>'char','size'=>200),
        'xar_desc'              =>array('null'=>true, 'type'=>'text','size'=>'large', 'default'=>''),
        'xar_order'             =>array('null'=>true, 'type'=>'integer')
    );
    $query = xarDBCreateTable($table_items, $fields);
    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Create a unique key on the name column.
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_lists_name',
        'fields'    => array('xar_name'),
        'unique'    => true
    );
    $query = xarDBCreateIndex($table_types,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create a unique key on the code column.
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_items_code',
        'fields'    => array('xar_code'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($table_items,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;


    // Create a unique index on the lid and code columns.
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_u1',
        'fields'    => array('xar_lid','xar_code'),
        'unique'    => true
    );
    $query = xarDBCreateIndex($table_items,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
     *
     * TODO MichelV: What to place privileges on?
     *      List types
     *      Lists
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
 * Delete the lists module
 *
 */
function lists_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $query = xarDBDropTable($xartable['lists_types']);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['lists_items']);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>