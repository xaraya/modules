<?php

/**
 *
 * Initialization of tasks module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage tasks
 */


/**
 * initialization functions
 * Initialise the Tasks module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author Chad Kraeft
 *
 */
function tasks_init()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();

    $tasktable = $xartables['tasks'];
    $taskcolumn = &$xartables['tasks_columns'];

    $fields = array (
                     'xar_id'                => array('type'=>'integer','null'=>false, 'increment'=>true,'primary_key'=>true),
                     'xar_parentid'          => array('type'=>'integer','null'=>false, 'default'=>'0'),
                     'xar_modname'           => array('type'=>'varchar','null'=>false,'default'=>'','size'=>255),
                     'xar_objectid'          => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_name'              => array('type'=>'varchar','null'=>false,'default'=>'','size'=>255),
                     'xar_description'       => array('type'=>'blob'),
                     'xar_status'            => array('type'=>'integer','null'=>false,'default'=>'0','size'=>'tiny'),
                     'xar_priority'          => array('type'=>'integer','null'=>false,'default'=>'0','size'=>'tiny'),
                     'xar_private'           => array('type'=>'integer','null'=>false,'default'=>'0','size'=>'tiny'),
                     'xar_creator'           => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_owner'             => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_assigner'          => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_created'      => array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_date_approved'     => array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_date_changed'      => array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_date_start_planned'=> array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_date_start_actual' => array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_date_end_planned'  => array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_date_end_actual'   => array('type'=>'integer'   ,'null'=>false,'default'=>''),
                     'xar_hours_planned'     => array('type'=>'float'  ,'null'=>false,'default'=>'0.0','width'=>8,'decimals'=>2),
                     'xar_hours_spent'       => array('type'=>'float'  ,'null'=>false,'default'=>'0.0','width'=>8,'decimals'=>2),
                     'xar_hours_remaining'   => array('type'=>'float'  ,'null'=>false,'default'=>'0.0','width'=>8,'decimals'=>2)
                     );

    $query = xarDBCreateTable($tasktable, $fields);
    $res =& $dbconn->Execute($query);
    if (!$res) return;

    xarModSetVar('tasks', 'dateformat', 0);
    xarModSetVar('tasks', 'showoptions', 0);
    xarModSetVar('tasks', 'returnfromadd', 1);
    xarModSetVar('tasks', 'returnfromedit', 0);
    xarModSetVar('tasks', 'returnfromsurface', 1);
    xarModSetVar('tasks', 'returnfrommigrate', 0);
    xarModSetVar('tasks', 'maxdisplaydepth', 9);

    return true;
}

function tasks_upgrade($oldversion)
{
    switch($oldversion) {
        case 0.1:
            break;
        case 0.2:
        case '0.2':
        case 1.0:
            break;
        case 2.0:
            break;
    }

    return true;
}

function tasks_delete()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $query = xarDBDropTable($xartable['tasks']);
    $res =& $dbconn->Execute($query);
    if (!$res) return;

    xarModDelAllVars('simpleadmin');

    return true;
}

?>