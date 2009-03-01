<?php
/**
 * XTask Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XTask Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
/**
 * initialise the xtasks module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xtasks_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $xtasks_table = $xartable['xtasks'];

    $xtasks_fields = array(
        'taskid'                =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'parentid'              =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'dependentid'           =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'projectid'             =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'modid'                 =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'itemtype'              =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'objectid'              =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'task_name'             =>array('type'=>'varchar','size'=>255,'null'=>FALSE),
        'status'                =>array('type'=>'varchar','size'=>32, 'null'=>FALSE),
        'priority'              =>array('type'=>'integer','null'=>FALSE, 'size'=>'tiny', 'default'=>'1'),
        'importance'            =>array('type'=>'integer','null'=>FALSE, 'size'=>'tiny', 'default'=>'1'),
        'description'           =>array('type'=>'text'),
        'private'               =>array('type'=>'integer','null'=>TRUE, 'size'=>'tiny', 'default'=>'0'),
        'creator'               =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'owner'                 =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'assigner'              =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'groupid'               =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'date_created'          =>array('type'=>'date','null'=>TRUE),
        'date_approved'         =>array('type'=>'date','null'=>TRUE),
        'date_changed'          =>array('type'=>'date','null'=>TRUE),
        'date_start_planned'    =>array('type'=>'date','null'=>TRUE),
        'date_start_actual'     =>array('type'=>'date','null'=>TRUE),
        'date_end_planned'      =>array('type'=>'date','null'=>TRUE),
        'date_end_actual'       =>array('type'=>'date','null'=>TRUE),
        'hours_planned'         =>array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
        'hours_spent'           =>array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
        'hours_remaining'       =>array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2));

    $sql = xarDBCreateTable($xtasks_table,$xtasks_fields);
    if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_parentid',
                   'fields'    => array('parentid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_projectid',
                   'fields'    => array('projectid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_modid',
                   'fields'    => array('modid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_itemtype',
                   'fields'    => array('itemtype'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_objectid',
                   'fields'    => array('objectid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_creator',
                   'fields'    => array('creator'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_owner',
                   'fields'    => array('owner'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_assigner',
                   'fields'    => array('assigner'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_groupid',
                   'fields'    => array('groupid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xartable['xtasks'],$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $reminders_table = $xartable['xtasks_reminders'];

    $reminders_fields = array(
        'reminderid'        =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'taskid'            =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'ownerid'           =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'eventdate'         =>array('type'=>'datetime','null'=>TRUE),
        'warning'           =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'reminder'          =>array('type'=>'text'));

    $sql = xarDBCreateTable($reminders_table,$reminders_fields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $worklog_table = $xartable['xtasks_worklog'];

    $worklog_fields = array(
        'worklogid'         =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'taskid'            =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'ownerid'           =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'eventdate'         =>array('type'=>'datetime','null'=>TRUE),
        'hours'             =>array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
        'notes'             =>array('type'=>'text'));

    $sql = xarDBCreateTable($worklog_table,$worklog_fields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;

    if (!$ddata_is_available) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }

    $xtasks_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xtasks/xardata/tasks.xml'));
    if (empty($xtasks_objectid)) return;
    xarModSetVar('xtasks','xtasks_objectid',$xtasks_objectid);
    
    $worklog_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xtasks/xardata/worklog.xml'));
    if (empty($worklog_objectid)) return;
    xarModSetVar('xtasks','worklog_objectid',$worklog_objectid);

    $reminders_objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xtasks/xardata/reminders.xml'));
    if (empty($reminders_objectid)) return;
    xarModSetVar('xtasks','reminders_objectid',$reminders_objectid);

    $usersettings = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xtasks/xardata/usersettings.xml'));
    if (empty($usersettings)) return;
    xarModSetVar('xtasks','usersettings',$usersettings);

    $modulesettings = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/xtasks/xardata/modulesettings.xml'));
    if (empty($modulesettings)) return;
    xarModSetVar('xtasks','modulesettings',$modulesettings);

    xarModSetVar('xtasks', 'dateformat', '');
    xarModSetVar('xtasks', 'autorefresh', 600);
    xarModSetVar('xtasks', 'displaytitle', 'Task Manager Administration');
    xarModSetVar('xtasks', 'leadtime', 3);
    xarModSetVar('xtasks', 'itemsperpage', 20);
    xarModSetVar('xtasks', 'prioritymax', 10);

    xarModSetVar('xtasks', 'SupportShortURLs', 0);
    /* If you provide short URL encoding functions you might want to also
     * provide module aliases and have them set in the module's administration.
     * Use the standard module var names for useModuleAlias and aliasname.
     */
    xarModSetVar('xtasks', 'useModuleAlias',false);
    xarModSetVar('xtasks', 'aliasname','');

//    xarBlockTypeRegister('xtasks', 'first');
//    xarBlockTypeRegister('xtasks', 'others');
    
    if (!xarModRegisterHook('item', 'usermenu', 'GUI','xtasks', 'user', 'usermenu'))
        return false;

    if (!xarModRegisterHook('item', 'display', 'GUI', 'xtasks', 'admin', 'workspace')) {
        return false;
    }
    
    if (!xarModRegisterHook('item', 'delete', 'API', 'xtasks', 'admin', 'delete')) {
        return false;
    }
    
    if (!xarModRegisterHook('module', 'remove', 'API', 'xtasks', 'admin', 'deleteall')) {
        return false;
    }
    
    xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'xtasks', 'hookModName' => 'xtasks'));

    $query1 = "SELECT DISTINCT $xartable[xtasks].modid
                          FROM $xartable[xtasks]
                     LEFT JOIN $xartable[modules]
                            ON $xartable[xtasks].modid = $xartable[modules].xar_regid";

    $query2 = "SELECT DISTINCT objectid FROM $xartable[xtasks]";

    $query3 = "SELECT DISTINCT taskid FROM $xartable[xtasks]";
    
    $instances = array(
                        array('header' => 'Module ID:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Module Page ID:',
                                'query' => $query2,
                                'limit' => 20
                            ),
                        array('header' => 'Task ID:',
                                'query' => $query3,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('xtasks','All',$instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');

    xarRegisterPrivilege('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
    xarRegisterPrivilege('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterPrivilege('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
    xarRegisterPrivilege('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterPrivilege('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');
    
    xarRegisterPrivilege('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    
    xarRegisterPrivilege('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    
    xarRegisterPrivilege('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');

    xarRegisterPrivilege('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    xarRegisterMask('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    
    return true;
}

function xtasks_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    
    $ddata_is_available = xarModIsAvailable('dynamicdata');
    if (!isset($ddata_is_available)) return;

    if (!$ddata_is_available) {
        $msg = xarML('Please activate the Dynamic Data module first...');
        xarErrorSet(XAR_USER_EXCEPTION, 'MODULE_NOT_ACTIVE',
                        new DefaultUserException($msg));
        return;
    }
            
    /* preserve importance, status and project type lists during ddata rewrite */
    $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
    $prop_data_cached = xarSessionGetVar('prop_data_cached');
    if($xtasks_objectid && !$prop_data_cached) {
        $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                array('objectid' => $xtasks_objectid));
        if($fields) {
            foreach ($fields as $name => $info) {
                if($name == "importance") {
                    $oldprop_importance = $info;
                    xarSessionSetVar('oldprop_importance', $info);
                }
                if($name == "status") {
                    $oldprop_status = $info;
                    xarSessionSetVar('oldprop_status', $info);
                }
            }
            xarSessionSetVar('prop_data_cached', 1);
        }
    }
    
    switch($oldversion) {

        case '1.0':
            // ADD HOOKS TO DELETE TASKS WHEN PARENT OBJECT IS DELETED
            if (!xarModRegisterHook('item', 'delete', 'API', 'xtasks', 'admin', 'delete')) {
                return false;
            }
            
            // ADD HOOKS TO DELETE TASKS WHEN PARENT OBJECT MODULE IS DELETED
            if (!xarModRegisterHook('module', 'remove', 'API', 'xtasks', 'admin', 'deleteall')) {
                return false;
            }
            
            // RESET INSTANCES TO FIX MODID RETRIEVAL    
            xarRemoveInstances('xtasks');

            $query1 = "SELECT DISTINCT $xartable[xtasks].modid
                                  FROM $xartable[xtasks]
                             LEFT JOIN $xartable[modules]
                                    ON $xartable[xtasks].modid = $xartable[modules].xar_regid";
        
            $query2 = "SELECT DISTINCT objectid FROM $xartable[xtasks]";
        
            $query3 = "SELECT DISTINCT taskid FROM $xartable[xtasks]";
            
            $instances = array(
                                array('header' => 'Module ID:',
                                        'query' => $query1,
                                        'limit' => 20
                                    ),
                                array('header' => 'Module Page ID:',
                                        'query' => $query2,
                                        'limit' => 20
                                    ),
                                array('header' => 'Task ID:',
                                        'query' => $query3,
                                        'limit' => 20
                                    )
                            );
            xarDefineInstance('xtasks','All',$instances);
        
        case '1.1':

            $reminders_table = $xartable['xtasks_reminders'];
        
            $reminders_fields = array(
                'reminderid'            =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                'taskid'                =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
                'ownerid'                =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
                'eventdate'                =>array('type'=>'date','null'=>TRUE),
                'reminder'                =>array('type'=>'text'));
        
            $sql = xarDBCreateTable($reminders_table,$reminders_fields);
            if (empty($sql)) return;
            $dbconn->Execute($sql);
            if ($dbconn->ErrorNo() != 0) {
                $msg = xarML('DATABASE_ERROR', $sql);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                               new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
                return;
            }

            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/usersettings.xml'));
            if (empty($objectid)) return;
            xarModSetVar('xtasks','usersettings',$objectid);
            
        case '1.2':

            $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
            if (!empty($xtasks_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $xtasks_objectid));
            }
            $xtasks_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/tasks.xml'));
            if (empty($xtasks_objectid)) return;
            xarModSetVar('xtasks','xtasks_objectid',$xtasks_objectid);

            $objectid = xarModGetVar('xtasks','modulesettings');
            if (!empty($xtasks_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
            }
            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/modulesettings.xml'));
            if (empty($objectid)) return;
            xarModSetVar('xtasks','modulesettings',$objectid);

            xarRegisterPrivilege('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
            xarRegisterPrivilege('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
            xarRegisterPrivilege('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
            xarRegisterPrivilege('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
            xarRegisterPrivilege('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
            xarRegisterPrivilege('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');

        case '1.3':
            // modify creator/owner/assigner fields
            $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
            if (!empty($xtasks_objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $xtasks_objectid));
            }
            $xtasks_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/tasks.xml'));
            if (empty($xtasks_objectid)) return;
            xarModSetVar('xtasks','xtasks_objectid',$xtasks_objectid);

        case '1.4':
        case '1.5':            
        case '1.5.1':
        case '1.5.2':
        case '1.5.3':
        case '1.5.4':
        case '1.5.5':
        case '1.5.6':
        case '1.5.7':
        case '1.5.8':
        case '1.5.9':
        case '1.5.10':
        case '1.5.11':
        case '1.5.12':
        case '1.5.13':
        case '1.5.14':
        case '1.5.15':
        
            // RELOAD TASK OBJECT
            $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
            if (!empty($xtasks_objectid)) {
//                $test1 = xarModAPIFunc('dynamicdata','user','getobject',array('objectid' => $xtasks_objectid));
                if(xarModAPIFunc('dynamicdata',
                                'admin',
                                'deleteobject',
                                array('objectid' => $xtasks_objectid,
                                    'module' => "xproject",
                                    'itemtype' => 1))) {
                    xarModSetVar('xtasks','xtasks_objectid', '');
                }
            }
            $xtasks_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/tasks.xml'));
            if (empty($xtasks_objectid)) {
                return;
            }
            xarModSetVar('xtasks','xtasks_objectid',$xtasks_objectid);
        
            // RELOAD USER SETTINGS
            $usersettings = xarModGetVar('xtasks','usersettings');
            if (!empty($usersettings)) {
//                $test2 = xarModAPIFunc('dynamicdata','user','getobject',array('objectid' => $usersettings));
                if(xarModAPIFunc('dynamicdata',
                                'admin',
                                'deleteobject',
                                array('objectid' => $usersettings,
                                    'module' => "Roles",
                                    'itemtype' => 704))) {
                    xarModSetVar('xtasks','usersettings', '');
                }
            }
            $usersettings = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/usersettings.xml'));
            if (empty($usersettings)) return;
            xarModSetVar('xtasks','usersettings',$usersettings);
            
            // RELOAD MODULE SETTINGS
            $modulesettings = xarModGetVar('xtasks','modulesettings');
            if (!empty($modulesettings)) {
//                $test3 = xarModAPIFunc('dynamicdata','user','getobject',array('objectid' => $modulesettings));
                if(xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings))) {
                    xarModSetVar('xtasks','modulesettings', '');
                }
            }
            $modulesettings = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/modulesettings.xml'));
            if (empty($modulesettings)) return;
            xarModSetVar('xtasks','modulesettings',$modulesettings);

        case '1.5.16':
    
            $worklog_table = $xartable['xtasks_worklog'];
        
            $worklog_fields = array(
                'worklogid'                =>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                'taskid'                =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
                'ownerid'                =>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
                'eventdate'                =>array('type'=>'date','null'=>TRUE),
                'hours'                    =>array('type'=>'float', 'size' =>'decimal', 'width'=>6, 'decimals'=>2),
                'notes'                    =>array('type'=>'text'));
        
            $sql = xarDBCreateTable($worklog_table,$worklog_fields);
            if (empty($sql)) return;
            $dbconn->Execute($sql);
            if ($dbconn->ErrorNo() != 0) {
                $msg = xarML('DATABASE_ERROR', $sql);
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                               new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
                return;
            }
            
        case '1.6':
    
            xarRegisterPrivilege('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
        case '1.6.1':
        
            $worklog_objectid = xarModGetVar('xtasks','worklog_objectid');
            if (!empty($worklog_objectid)) {
                if(xarModAPIFunc('dynamicdata',
                                'admin',
                                'deleteobject',
                                array('objectid' => $worklog_objectid,
                                    'module' => "xtasks",
                                    'itemtype' => 2))) {
                    xarModSetVar('xtasks','worklog_objectid', '');
                }
            }
            $worklog_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/worklog.xml'));
            if (empty($worklog_objectid)) {
                return;
            }
            xarModSetVar('xtasks','worklog_objectid',$worklog_objectid);

        case '1.6.2':
    
            xarRegisterPrivilege('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
    
            xarRegisterPrivilege('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
        case '1.6.3':

            $reminders_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/reminders.xml'));
            if (empty($reminders_objectid)) return;
            xarModSetVar('xtasks','reminders_objectid',$reminders_objectid);

        case '1.6.4':
    
            xarRegisterPrivilege('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
        case '1.6.5':
        case '1.6.6':

            $reminders_objectid = xarModGetVar('xtasks','reminders_objectid');
            if (!empty($reminders_objectid)) {
                if(xarModAPIFunc('dynamicdata',
                                'admin',
                                'deleteobject',
                                array('objectid' => $reminders_objectid,
                                    'module' => "xtasks",
                                    'itemtype' => 3))) {
                    xarModSetVar('xtasks','reminders_objectid', '');
                }
            }
            $reminders_objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/reminders.xml'));
            if (empty($reminders_objectid)) return;
            xarModSetVar('xtasks','reminders_objectid',$reminders_objectid);
            
        case '1.6.7':

            $reminders_table = $xartable['xtasks_reminders'];
            $result = $datadict->alterColumn($reminders_table, 'eventdate T');
            if (!$result) return;
            
        case '1.6.8':

            $worklog_table = $xartable['xtasks_worklog'];
            $result = $datadict->alterColumn($worklog_table, 'eventdate T');
            if (!$result) return;
            
        case '1.6.9':
        case '1.6.10':
        case '1.6.11':
        case '1.6.12':
            $reminders_table = $xartable['xtasks_reminders'];
            $result = $datadict->addColumn($reminders_table, 'reminder X');
            if(xarCurrentErrorType() == 2) xarErrorFree();
            $result = $datadict->addColumn($reminders_table, 'warning I(11) NotNull Default 0');
            if(xarCurrentErrorType() == 2) xarErrorFree();
        case '1.6.13':
        case '1.7':
        
            // RELOAD USER SETTINGS
            $usersettings = xarModGetVar('xtasks','usersettings');
            if (!empty($usersettings)) {
//                $test2 = xarModAPIFunc('dynamicdata','user','getobject',array('objectid' => $usersettings));
                if(xarModAPIFunc('dynamicdata',
                                'admin',
                                'deleteobject',
                                array('objectid' => $usersettings,
                                    'module' => "Roles",
                                    'itemtype' => 704))) {
                    xarModSetVar('xtasks','usersettings', '');
                }
            }
            $usersettings = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/xtasks/xardata/usersettings.xml'));
            if (empty($usersettings)) return;
            xarModSetVar('xtasks','usersettings',$usersettings);
        
        case '1.7.1':
            xarRemoveMasks('xtasks');
            xarRemoveInstances('xtasks');

            xarRegisterMask('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
            xarRegisterMask('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
            xarRegisterMask('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
            xarRegisterMask('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
            xarRegisterMask('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');
        
            xarRegisterPrivilege('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
            xarRegisterPrivilege('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
            xarRegisterPrivilege('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
            xarRegisterPrivilege('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
            xarRegisterPrivilege('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
            xarRegisterPrivilege('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');
            
            xarRegisterPrivilege('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
            xarRegisterPrivilege('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
            xarRegisterPrivilege('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
        
            xarRegisterPrivilege('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
        case '1.7.2':
        
            $xtasks_table = $xartable['xtasks'];
            $result = $datadict->addColumn($xtasks_table, 'dependentid I(11) NotNull Default 0');
            if(xarCurrentErrorType() == 2) xarErrorFree();
        
        case '1.7.3':
            xarRemoveMasks('xtasks');
            xarRemoveInstances('xtasks');

            xarRegisterMask('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
            xarRegisterMask('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
            xarRegisterMask('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
            xarRegisterMask('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
            xarRegisterMask('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');
        
            xarRegisterPrivilege('ViewXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_OVERVIEW');
            xarRegisterPrivilege('ReadXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_READ');
            xarRegisterPrivilege('EditXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_EDIT');
            xarRegisterPrivilege('AddXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADD');
            xarRegisterPrivilege('DeleteXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_DELETE');
            xarRegisterPrivilege('AdminXTask', 'All', 'xtasks', 'All', 'All', 'ACCESS_ADMIN');
            
            xarRegisterPrivilege('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('UseReminders', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
            xarRegisterPrivilege('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('ViewWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            
            xarRegisterPrivilege('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('RecordWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
        
            xarRegisterPrivilege('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
            xarRegisterMask('AuditWorklog', 'All', 'xtasks', 'All', 'All', 'ACCESS_COMMENT');
        
        
        case '1.7.4':
                
            /* restore importance, status, and project type lists */
            if($prop_data_cached) {
                $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                        array('objectid' => $xtasks_objectid));
                foreach ($fields as $name => $info) {
                    if($name == "importance") {
                        $newprop_importance = $info;
                    }
                    if($name == "status") {
                        $newprop_status = $info;
                    }
                }
                
                $oldprop_status = xarSessionGetVar('oldprop_status');
                $oldprop_importance = xarSessionGetVar('oldprop_importance');
                $oldprop_projecttype = xarSessionGetVar('oldprop_projecttype');
                
                
                if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                  array('prop_id' => $newprop_status['id'],
                                        'label' => $oldprop_status['label'],
                                        'type' => $oldprop_status['type'],
                                        'default' => $oldprop_status['default'],
                                        'status' => $oldprop_status['status'],
                                        'validation' => $oldprop_status['validation']))) {
                    return;
                }
                
                if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                  array('prop_id' => $newprop_importance['id'],
                                        'label' => $oldprop_importance['label'],
                                        'type' => $oldprop_importance['type'],
                                        'default' => $oldprop_importance['default'],
                                        'status' => $oldprop_importance['status'],
                                        'validation' => $oldprop_importance['validation']))) {
                    return;
                }
                xarSessionDelVar('prop_data_cached');
            }
        case '1.8.0':
        
    
            if (!xarModRegisterHook('item', 'usermenu', 'GUI','xtasks', 'user', 'usermenu'))
                return false;
        
        case '1.8.1':
            break;

    }

    return true;
}

function xtasks_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    $sql = xarDBDropTable($xartable['xtasks']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['xtasks_reminders']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['xtasks_worklog']);
    if (empty($sql)) return;
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
    if (!empty($xtasks_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $xtasks_objectid));
    }
    xarModDelVar('xtasks','xtasks_objectid');

    $worklog_objectid = xarModGetVar('xtasks','worklog_objectid');
    if (!empty($worklog_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $worklog_objectid));
    }
    xarModDelVar('xtasks','worklog_objectid');

    $reminders_objectid = xarModGetVar('xtasks','reminders_objectid');
    if (!empty($reminders_objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $reminders_objectid));
    }
    xarModDelVar('xtasks','reminders_objectid');

    $usersettings = xarModGetVar('xtasks','usersettings');
    if (!empty($usersettings)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $usersettings));
    }
    xarModDelVar('xtasks','usersettings');

    $modulesettings = xarModGetVar('xtasks','modulesettings');
    if (!empty($modulesettings)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $modulesettings));
    }
    xarModDelVar('xtasks','modulesettings');
    
     /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('xtasks','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='xtasks')){
        xarModDelAlias($aliasname,'xtasks');
    }
    
    /* Delete any module variables */
    xarModDelAllVars('xtasks');
 
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                            'xtasks', 'user', 'workspace')) {
        return false;
    }

    //xarBlockTypeUnregister('xtasks', 'first');
    //xarBlockTypeUnregister('xtasks', 'others');

    /* Remove Masks and Instances
     * these functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('xtasks');
    xarRemoveInstances('xtasks');

    return true;
}

?>
