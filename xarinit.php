<?php
/**
 * Workflow initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author mikespub
 */
/**
 * initialise the workflow module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function workflow_init()
{
    if (!xarVarFetch('loadexample', 'checkbox', $loadexample, 1, XARVAR_NOT_REQUIRED)) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // Galaxia developers use quotes around column names.
    // Since PostgreSQL creates column names in lowercase by
    // default, the column names must be surrounded by quotes.
    $dbtype  = xarCore_getSystemVar('DB.Type');
    switch ($dbtype) {
        case 'postgres':
                $qte = '"';
            break;
        case 'mysql':
        default:
                $qte = '';
            break;
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
        $qte.'activityId'.$qte        => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'name'.$qte              => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        $qte.'normalized_name'.$qte   => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        $qte.'pId'.$qte               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'type'.$qte              => array('type'=>'varchar','size'=>20,'null'=>TRUE),
        $qte.'isAutoRouted'.$qte      => array('type'=>'char','size'=>1,'null'=>TRUE),
        $qte.'flowNum'.$qte           => array('type'=>'integer','null'=>TRUE),
        $qte.'isInteractive'.$qte     => array('type'=>'char','size'=>1,'null'=>TRUE),
        $qte.'lastModif'.$qte         => array('type'=>'integer','null'=>TRUE),
        $qte.'description'.$qte       => array('type'=>'text','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'activityId'.$qte        => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        $qte.'roleId'.$qte            => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'instanceId'.$qte        => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        $qte.'activityId'.$qte        => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        $qte.'started'.$qte           => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'ended'.$qte             => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'user'.$qte              => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        $qte.'status'.$qte            => array('type'=>'varchar','size'=>20,'null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'cId'.$qte               => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'instanceId'.$qte        => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'user'.$qte              => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        $qte.'activityId'.$qte        => array('type'=>'integer','null'=>TRUE),
        $qte.'hash'.$qte              => array('type'=>'varchar','size'=>32,'null'=>TRUE),
        $qte.'title'.$qte             => array('type'=>'varchar','size'=>250,'null'=>TRUE),
        $qte.'comment'.$qte           => array('type'=>'text','null'=>TRUE),
        $qte.'activity'.$qte          => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        $qte.'timestamp'.$qte         => array('type'=>'integer','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'instanceId'.$qte        => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'pId'.$qte               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'started'.$qte           => array('type'=>'integer','null'=>TRUE),
        $qte.'owner'.$qte             => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        $qte.'nextActivity'.$qte      => array('type'=>'integer','null'=>TRUE),
        $qte.'nextUser'.$qte          => array('type'=>'varchar','size'=>200,'null'=>TRUE),
        $qte.'ended'.$qte             => array('type'=>'integer','null'=>TRUE),
        $qte.'status'.$qte            => array('type'=>'varchar','size'=>20,'null'=>TRUE),
        $qte.'properties'.$qte        => array('type'=>'blob','null'=>TRUE),
        $qte.'name'.$qte              => array('type'=>'varchar','size'=>80,'null'=>FALSE,'default'=>'')
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'pId'.$qte               => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'name'.$qte              => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        $qte.'isValid'.$qte           => array('type'=>'char','size'=>1,'null'=>TRUE),
        $qte.'isActive'.$qte          => array('type'=>'char','size'=>1,'null'=>TRUE),
        $qte.'version'.$qte           => array('type'=>'varchar','size'=>12,'null'=>TRUE),
        $qte.'description'.$qte       => array('type'=>'text','null'=>TRUE),
        $qte.'lastModif'.$qte         => array('type'=>'integer','null'=>TRUE),
        $qte.'normalized_name'.$qte   => array('type'=>'varchar','size'=>80,'null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'roleId'.$qte            => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'pId'.$qte               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'lastModif'.$qte         => array('type'=>'integer','null'=>TRUE),
        $qte.'name'.$qte              => array('type'=>'varchar','size'=>80,'null'=>TRUE),
        $qte.'description'.$qte       => array('type'=>'text','null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'pId'.$qte               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'actFromId'.$qte         => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE),
        $qte.'actToId'.$qte           => array('type'=>'integer','null'=>FALSE,'default'=>'0','primary_key'=>TRUE)
    );


    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'pId'.$qte               => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'roleId'.$qte            => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'user'.$qte              => array('type'=>'varchar','size'=>200,'null'=>FALSE,'default'=>'','primary_key'=>TRUE)
    );


    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
        $qte.'itemId'.$qte            => array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        $qte.'instanceId'.$qte        => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'orderId'.$qte           => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'activityId'.$qte        => array('type'=>'integer','null'=>FALSE,'default'=>'0'),
        $qte.'type'.$qte              => array('type'=>'varchar','size'=>20,'null'=>TRUE),
        $qte.'properties'.$qte        => array('type'=>'blob','null'=>TRUE),
        $qte.'started'.$qte           => array('type'=>'integer','null'=>TRUE),
        $qte.'ended'.$qte             => array('type'=>'integer','null'=>TRUE),
        $qte.'user'.$qte              => array('type'=>'varchar','size'=>200,'null'=>TRUE)
    );

    // Create the table DDL
    $query = xarDBCreateTable($table, $fields);
    if (empty($query)) return false; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
 * @return bool true on success
 */
function workflow_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.5':
        case '1.5.0':
            // Code to upgrade from version 1.5.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the workflow module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
function workflow_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

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

    // Remove all process files
    workflow_remove_processes();

    // Remove Masks and Instances
    xarRemoveMasks('workflow');
    xarRemoveInstances('workflow');

    // Deletion successful
    return true;
}

function workflow_remove_processes()
{
    sys::import('modules.workflow.lib.galaxia.config');
    $dir = GALAXIA_PROCESSES;
    if (!is_dir($dir)) return;
    $h = opendir($dir);
    while(($file = readdir($h)) != false) {
        if (is_dir($dir.'/'.$file) && $file != '.' && $file != '..') {
            workflow_remove_directory($dir.'/'.$file);
        }
    }
    closedir($h);
}

function workflow_remove_directory($dir)
{
    if (!is_dir($dir)) return;
    $h = opendir($dir);
    while(($file = readdir($h)) != false) {
        if (is_file($dir.'/'.$file)) {
            @unlink($dir.'/'.$file);
        } else {
            if (is_dir($dir.'/'.$file) && $file != '.' && $file != '..') {
              workflow_remove_directory($dir.'/'.$file);
            }
        }
    }
    closedir($h);
    @rmdir($dir);
    if(file_exists($dir)) @unlink($dir);
}

?>