<?php
/**
 * File: $Id$
 * 
 * Workflow initialization functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage workflow
 * @author mikespub
 */

/**
 * initialise the workflow module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function workflow_init()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // 'user' is a reserved word in PostgreSQL and must be surrounded
    // by double quotes to be used as a column name
    $dbtype  = xarCore_getSystemVar('DB.Type');
    if ($dbtype == 'postgres') {
        $usercol = '"user"';
    } else {
        $usercol = 'user';
    }

// From file db/tiki.sql of TikiWiki 1.8 in CVS :
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_activities] (
  activityId int(14) NOT NULL auto_increment,
  name varchar(80) default NULL,
  normalized_name varchar(80) default NULL,
  pId int(14) NOT NULL default '0',
  type enum('start','end','split','switch','join','activity','standalone') default NULL,
  isAutoRouted char(1) default NULL,
  flowNum int(10) default NULL,
  isInteractive char(1) default NULL,
  lastModif int(14) default NULL,
  description text,
  PRIMARY KEY  (activityId)
)";
    */

    // Create table workflow_activities
    $table = $xartable['workflow_activities'];

    $fields = array(
        'activityId'        => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'name'              => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        'normalized_name'   => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        'pId'               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'type'              => array('type'=>'varchar','size'=>20,'null'=>TRUE),
        'isAutoRouted'      => array('type'=>'char','size'=>1,'null'=>TRUE),
        'flowNum'           => array('type'=>'integer','null'=>TRUE),
        'isInteractive'     => array('type'=>'char','size'=>1,'null'=>TRUE),
        'lastModif'         => array('type'=>'integer','null'=>TRUE),
        'description'       => array('type'=>'text','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_activity_roles] (
  activityId int(14) NOT NULL default '0',
  roleId int(14) NOT NULL default '0',
  PRIMARY KEY  (activityId,roleId)
)";
    */
    // Create table workflow_activity_roles
    $table = $xartable['workflow_activity_roles'];

    $fields = array(
        'activityId'        => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        'roleId'            => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_instance_activities] (
  instanceId int(14) NOT NULL default '0',
  activityId int(14) NOT NULL default '0',
  started int(14) NOT NULL default '0',
  ended int(14) NOT NULL default '0',
  user varchar(200) default NULL,
  status enum('running','completed') default NULL,
  PRIMARY KEY  (instanceId,activityId)
)";
    */

    // Create table workflow_instance_activities
    $table = $xartable['workflow_instance_activities'];

    $fields = array(
        'instanceId'        => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        'activityId'        => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        'started'           => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'ended'             => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $usercol            => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        'status'            => array('type'=>'varchar','size'=>20,'null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_instance_comments] (
  cId int(14) NOT NULL auto_increment,
  instanceId int(14) NOT NULL default '0',
  user varchar(200) default NULL,
  activityId int(14) default NULL,
  hash varchar(32) default NULL,
  title varchar(250) default NULL,
  comment text,
  activity varchar(80) default NULL,
  timestamp int(14) default NULL,
  PRIMARY KEY  (cId)
)";
    */

    // Create table workflow_instance_comments
    $table = $xartable['workflow_instance_comments'];

    $fields = array(
        'cId'               => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'instanceId'        => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $usercol            => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        'activityId'        => array('type'=>'integer','null'=>TRUE),
        'hash'              => array('type'=>'varchar','size'=>32,'null'=>TRUE),
        'title'             => array('type'=>'varchar','size'=>250,'null'=>TRUE),
        'comment'           => array('type'=>'text','null'=>TRUE),
        'activity'          => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        'timestamp'         => array('type'=>'integer','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_instances] (
  instanceId int(14) NOT NULL auto_increment,
  pId int(14) NOT NULL default '0',
  started int(14) default NULL,
  owner varchar(200) default NULL,
  nextActivity int(14) default NULL,
  nextUser varchar(200) default NULL,
  ended int(14) default NULL,
  status enum('active','exception','aborted','completed') default NULL,
  properties longblob,
  PRIMARY KEY  (instanceId)
)";
    */

    // Create table workflow_instances
    $table = $xartable['workflow_instances'];

    $fields = array(
        'instanceId'        => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'pId'               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'started'           => array('type'=>'integer','null'=>TRUE),
        'owner'             => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        'nextActivity'      => array('type'=>'integer','null'=>TRUE),
        'nextUser'          => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        'ended'             => array('type'=>'integer','null'=>TRUE),
        'status'            => array('type'=>'varchar','size'=>20,'null'=>TRUE),
        'properties'        => array('type'=>'blob','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_processes] (
  pId int(14) NOT NULL auto_increment,
  name varchar(80) default NULL,
  isValid char(1) default NULL,
  isActive char(1) default NULL,
  version varchar(12) default NULL,
  description text,
  lastModif int(14) default NULL,
  normalized_name varchar(80) default NULL,
  PRIMARY KEY  (pId)
)";
    */

    // Create table workflow_processes
    $table = $xartable['workflow_processes'];

    $fields = array(
        'pId'               => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'name'              => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        'isValid'           => array('type'=>'char','size'=>1,'null'=>TRUE),
        'isActive'          => array('type'=>'char','size'=>1,'null'=>TRUE),
        'version'           => array('type'=>'varchar','size'=>12,'null'=>TRUE),
        'description'       => array('type'=>'text','null'=>TRUE),
        'lastModif'         => array('type'=>'integer','null'=>TRUE),
        'normalized_name'   => array('type'=>'varchar','size'=>80,'null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_roles] (
  roleId int(14) NOT NULL auto_increment,
  pId int(14) NOT NULL default '0',
  lastModif int(14) default NULL,
  name varchar(80) default NULL,
  description text,
  PRIMARY KEY  (roleId)
)";
    */

    // Create table workflow_roles
    $table = $xartable['workflow_roles'];

    $fields = array(
        'roleId'            => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'pId'               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'lastModif'         => array('type'=>'integer','null'=>TRUE),
        'name'              => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        'description'       => array('type'=>'text','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_transitions] (
  pId int(14) NOT NULL default '0',
  actFromId int(14) NOT NULL default '0',
  actToId int(14) NOT NULL default '0',
  PRIMARY KEY  (actFromId,actToId)
)";
    */

    // Create table workflow_transitions
    $table = $xartable['workflow_transitions'];

    $fields = array(
        'pId'               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'actFromId'         => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        'actToId'           => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE)
    );


    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_user_roles] (
  pId int(14) NOT NULL default '0',
  roleId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  PRIMARY KEY  (roleId,user)
)";
    */

    // Create table workflow_user_roles
    $table = $xartable['workflow_user_roles'];

    $fields = array(
        'pId'               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'roleId'            => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $usercol            => array('type'=>'varchar','size'=>200,'null'=>FALSE,'default'=>'','primary_key'=>TRUE)
    );


    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    /*
    $queries[] =
"CREATE TABLE $xartable[workflow_workitems] (
  itemId int(14) NOT NULL auto_increment,
  instanceId int(14) NOT NULL default '0',
  orderId int(14) NOT NULL default '0',
  activityId int(14) NOT NULL default '0',
  properties longblob,
  started int(14) default NULL,
  ended int(14) default NULL,
  user varchar(200) default NULL,
  PRIMARY KEY  (itemId)
)";
    */

    // Create table workflow_workitems
    $table = $xartable['workflow_workitems'];

    $fields = array(
        'itemId'            => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'instanceId'        => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'orderId'           => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'activityId'        => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        'type'              => array('type'=>'varchar','size'=>20,'null'=>TRUE),
        'properties'        => array('type'=>'blob','null'=>TRUE),
        'started'           => array('type'=>'integer','null'=>TRUE),
        'ended'             => array('type'=>'integer','null'=>TRUE),
        $usercol            => array('type'=>'varchar','size'=>200,'null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($query);

    // Check for an error with the database
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                    new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }
    
    // set default activityId for create, update and delete hooks
    xarModSetVar('workflow','default.create',0);
    xarModSetVar('workflow','default.update',0);
    xarModSetVar('workflow','default.delete',0);

    xarModSetVar('workflow','SupportShortURLs',0);
    xarModSetVar('workflow','itemsperpage',20);
    xarModSetVar('workflow','seenlist','');

    if (!xarModRegisterHook('item', 'create', 'API',
                           'workflow', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
                           'workflow', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'workflow', 'admin', 'deletehook')) {
        return false;
    }
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'workflow', 'admin', 'removehook')) {
        return false;
    }

/* // TODO: show pending instances someday ?
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'workflow', 'user', 'usermenu')) {
        return false;
    }
*/

    // Register BL tags
    // show the output of a workflow activity in a template (e.g. shopping cart or whatever)
    xarTplRegisterTag('workflow', 'workflow-activity',
                      array(),
                      'workflow_userapi_handleactivitytag');

    // show the status (current activity/exception/aborted/completed) for "your" instances
    xarTplRegisterTag('workflow', 'workflow-status',
                      array(),
                      'workflow_userapi_handlestatustag');

    // show the instances that are assigned/accessible to you (i.e. your task list)
    xarTplRegisterTag('workflow', 'workflow-instances',
                      array(),
                      'workflow_userapi_handleinstancestag');

    // define privilege instances and masks
    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('workflow', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('workflow', 'Item', $instances);

// TODO: tweak this - allow viewing workflow of "your own items" someday ?
    xarRegisterMask('ReadWorkflow', 'All', 'workflow', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AdminWorkflow', 'All', 'workflow', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the workflow module from an old version
 * This function can be called multiple times
 */
function workflow_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here

            // set default activityId for create, update and delete hooks
            xarModSetVar('workflow','default.create',0);
            xarModSetVar('workflow','default.update',0);
            xarModSetVar('workflow','default.delete',0);

            xarModSetVar('workflow','SupportShortURLs',0);

            if (!xarModRegisterHook('item', 'create', 'API',
                                   'workflow', 'admin', 'createhook')) {
                return false;
            }
            if (!xarModRegisterHook('item', 'update', 'API',
                                   'workflow', 'admin', 'updatehook')) {
                return false;
            }
            if (!xarModRegisterHook('item', 'delete', 'API',
                                   'workflow', 'admin', 'deletehook')) {
                return false;
            }
            if (!xarModRegisterHook('module', 'remove', 'API',
                                   'workflow', 'admin', 'removehook')) {
                return false;
            }
            // fall through to next upgrade

        case 1.1:
            // Code to upgrade from version 1.1 goes here
            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();

            xarDBLoadTableMaintenanceAPI();

            $mytables = array(
                              'workflow_activities',
                              'workflow_activity_roles',
                              'workflow_instance_activities',
                              'workflow_instance_comments',
                              'workflow_instances',
                              'workflow_processes',
                              'workflow_roles',
                              'workflow_transitions',
                              'workflow_user_roles',
                              'workflow_workitems',
                             );
            foreach ($mytables as $mytable) {
                $oldname = preg_replace('/^workflow_/','galaxia_',$mytable);
                // Generate the SQL to rename the table using the API
                $query = xarDBAlterTable($oldname,
                                         array('command' => 'rename',
                                               'new_name' => $xartable[$mytable]));
                if (empty($query)) return false; // throw back

                // Rename the table and send exception if returns false.
                $result = &$dbconn->Execute($query);
                if (!$result) return false;
            }
            // fall through to next upgrade

        case 1.2:
            // Re-compile all activities with new compiler code
            include_once('modules/workflow/tiki-setup.php');
            include_once(GALAXIA_LIBRARY.'/ProcessManager.php');
            $all_procs = $processManager->list_processes(0, -1, 'pId_asc', '', '');
            if (!empty($all_procs) && count($all_procs['data']) > 0) {
                foreach ($all_procs['data'] as $info) {
                    $activities = $activityManager->list_activities($info['pId'], 0, -1, 'activityId_asc', '', '');
                    if (empty($activities) || count($activities['data']) < 1) continue;
                    foreach ($activities['data'] as $actinfo) {
                        $activityManager->compile_activity($info['pId'],$actinfo['activityId']);
                    }
                }
            }

            // Register BL tags
            // show the output of a workflow activity in a template (e.g. shopping cart or whatever)
            xarTplRegisterTag('workflow', 'workflow-activity',
                              array(),
                             'workflow_userapi_handleactivitytag');
            // show the status (current activity/exception/aborted/completed) for "your" instances
            xarTplRegisterTag('workflow', 'workflow-status',
                              array(),
                              'workflow_userapi_handlestatustag');

            xarModSetVar('workflow','seenlist','');
            // fall through to next upgrade

        case 1.3:
            // show the instances that are assigned/accessible to you (i.e. your task list)
            xarTplRegisterTag('workflow', 'workflow-instances',
                              array(),
                              'workflow_userapi_handleinstancestag');
            // fall through to next upgrade

        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the workflow module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function workflow_delete()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    $mytables = array(
                      'workflow_activities',
                      'workflow_activity_roles',
                      'workflow_instance_activities',
                      'workflow_instance_comments',
                      'workflow_instances',
                      'workflow_processes',
                      'workflow_roles',
                      'workflow_transitions',
                      'workflow_user_roles',
                      'workflow_workitems',
                     );

    foreach ($mytables as $mytable) {
        // Generate the SQL to drop the table using the API
        $query = xarDBDropTable($xartable[$mytable]);
        if (empty($query)) return false; // throw back

        // Drop the table and send exception if returns false.
        $result = &$dbconn->Execute($query);
        if (!$result) return false;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'workflow', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
                           'workflow', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                           'workflow', 'admin', 'deletehook')) {
        return false;
    }
    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModUnregisterHook('module', 'remove', 'API',
                           'workflow', 'admin', 'removehook')) {
        return false;
    }
/* // TODO: show pending instances someday ?
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'workflow', 'user', 'usermenu')) {
        return false;
    } 
*/

    // Unregister BL tags
    xarTplUnregisterTag('workflow-activity');
    xarTplUnregisterTag('workflow-status');
    xarTplUnregisterTag('workflow-instances');

    // Remove Masks and Instances
    xarRemoveMasks('workflow');
    xarRemoveInstances('workflow'); 

    // Deletion successful
    return true;
}

?>
