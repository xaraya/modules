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

// From file db/tiki.sql of TikiWiki 1.8 in CVS :

    $queries = array();

// TODO
//    $query = xarDBCreateTable($xartable['...'], ...);

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

    $queries[] =
"CREATE TABLE $xartable[workflow_activity_roles] (
  activityId int(14) NOT NULL default '0',
  roleId int(14) NOT NULL default '0',
  PRIMARY KEY  (activityId,roleId)
)";

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

    $queries[] =
"CREATE TABLE $xartable[workflow_roles] (
  roleId int(14) NOT NULL auto_increment,
  pId int(14) NOT NULL default '0',
  lastModif int(14) default NULL,
  name varchar(80) default NULL,
  description text,
  PRIMARY KEY  (roleId)
)";

    $queries[] =
"CREATE TABLE $xartable[workflow_transitions] (
  pId int(14) NOT NULL default '0',
  actFromId int(14) NOT NULL default '0',
  actToId int(14) NOT NULL default '0',
  PRIMARY KEY  (actFromId,actToId)
)";

    $queries[] =
"CREATE TABLE $xartable[workflow_user_roles] (
  pId int(14) NOT NULL default '0',
  roleId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  PRIMARY KEY  (roleId,user)
)";

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

    // create tables
    foreach ($queries as $query) {
        // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
        $result = &$dbconn->Execute($query);
        if (!$result) return;
    }

    // set default activityId for create, update and delete hooks
    xarModSetVar('workflow','default.create',0);
    xarModSetVar('workflow','default.update',0);
    xarModSetVar('workflow','default.delete',0);

    xarModSetVar('workflow','SupportShortURLs',0);
    xarModSetVar('workflow','itemsperpage',20);

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
                if (empty($query)) return; // throw back

                // Rename the table and send exception if returns false.
                $result = &$dbconn->Execute($query);
                if (!$result) return;
            }
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
        if (empty($query)) return; // throw back

        // Drop the table and send exception if returns false.
        $result = &$dbconn->Execute($query);
        if (!$result) return;
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

    // Remove Masks and Instances
    xarRemoveMasks('workflow');
    xarRemoveInstances('workflow'); 

    // Deletion successful
    return true;
}

?>
