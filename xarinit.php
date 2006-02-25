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
                     'xar_itemtype'          => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_name'              => array('type'=>'varchar','null'=>false,'default'=>'','size'=>255),
                     'xar_description'       => array('type'=>'blob'),
                     'xar_status'            => array('type'=>'integer','null'=>false,'default'=>'0','size'=>'tiny'),
                     'xar_priority'          => array('type'=>'integer','null'=>false,'default'=>'0','size'=>'tiny'),
                     'xar_private'           => array('type'=>'integer','null'=>false,'default'=>'0','size'=>'tiny'),
                     'xar_creator'           => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_owner'             => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_assigner'          => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_created'      => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_approved'     => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_changed'      => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_start_planned'=> array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_start_actual' => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_end_planned'  => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_date_end_actual'   => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_hours_planned'     => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_hours_spent'       => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_hours_applied'     => array('type'=>'integer','null'=>false,'default'=>'0'),
                     'xar_hours_remaining'   => array('type'=>'integer','null'=>false,'default'=>'0'),
                     );

    $query = xarDBCreateTable($tasktable, $fields);
    $res =& $dbconn->Execute($query);
    if (!$res) return;

# --------------------------------------------------------
# Create wrapper DD objects for the native itemtypes of the roles module
	if (!xarModAPIFunc('tasks','admin','createobjects')) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewTask','All','tasks','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminTask','All','tasks','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewTask','All','tasks','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('AdminTask','All','tasks','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up modvars
#
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
    //Remove the objects
	$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => 667, 'itemtype' => 1));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));
	$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => 667, 'itemtype' => 2));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));
	$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => 667, 'itemtype' => 3));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $query = xarDBDropTable($xartable['tasks']);
    $res =& $dbconn->Execute($query);
    if (!$res) return;

    xarRemoveMasks('tasks');
    xarRemoveInstances('tasks');
    xarModDelAllVars('tasks');

    return true;
}

?>