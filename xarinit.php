<?php
/**
 * Todolist initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */
/**
 * Todolist initialization functions
 * Initialise the todolist module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @Original Author of file: Jim McDonald
 * @author Todolist module development team
 * @author MichelV <michelv@xarayahosting.nl>
 */

function todolist_init()
{

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* It's good practice to name the table definitions you
     * are using - $table doesn't cut it in more complex modules
     */
    $group_memberstable = $xartable['todolist_group_members'];

    $fields = "xar_group_id I KEY default 0,
               xar_member_id I KEY default 0
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($group_memberstable, $fields);
    if (!$result) {return;}

    $groupstable = $xartable['todolist_groups'];

    $fields = "xar_id I PRIMARY auto,
               xar_group_name C(30) default NULL,
               xar_description C(200) default NULL,
               xar_group_leader I default NULL
               ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($groupstable, $fields);
    if (!$result) {return;}

    /* Can't we do this with comments ???
    $notestable = $xartable['todolist_notes'];
    $fields = "xar_todo_id I unsigned NOTNULL default 0,
            xar_note_id I unsigned PRIMARY auto,
            xar_text text,
            xar_usernr I default NULL,
            xar_date I NOTNULL default 0
            "; // Shouldn't date be a datetype?
    // Create or alter the table as necessary
    $result = $datadict->changeTable($notestable, $fields);
    if (!$result) {return;}
*/
    $project_memberstable = $xartable['todolist_project_members'];

    $fields = "xar_project_id I PRIMARY default 0,
              xar_member_id I PRIMARY default 0
              ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($project_memberstable, $fields);
    if (!$result) {return;}

    $projectstable = $xartable['todolist_projects'];

    $fields = "xar_project_id I UNSIGNED PRIMARY auto,
            xar_project_name C(30) default NULL,
            xar_description C(200) default NULL,
            xar_project_leader I NOTNULL default 0
            ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($projectstable, $fields);
    if (!$result) {return;}

    $responsible_groupstable = $xartable['todolist_responsible_groups'];

    $fields = "xar_todo_id I PRIMARY default 0,
               xar_group_id I PRIMARY default 0
              ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($responsible_groupstable, $fields);
    if (!$result) {return;}

    $responsible_personstable = $xartable['todolist_responsible_persons'];

    $fields = "xar_todo_id I PRIMARY default 0,
               xar_user_id I PRIMARY default 0
              ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($responsible_groupstable, $fields);
    if (!$result) {return;}

    $todostable = $xartable['todolist_todos'];

    $fields = "xar_todo_id      I    UNSIGNED     AUTO       PRIMARY,
            xar_project_id I unsigned NOT NULL default '0',
            xar_todo_text C(255) NOT NULL default '',
            xar_todo_priority I unsigned default NULL,
            xar_percentage_completed I2 unsigned NOTNULL default 0,
            xar_created_by I NOTNULL default 0,
            xar_due_date date default NULL,
            xar_date_created I NOTNULL default 0,
            xar_date_changed I NOTNULL default 0,
            xar_changed_by I NOTNULL default 0,
            xar_status I2 NOTNULL default 0
            ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($todostable, $fields);
    if (!$result) {return;}
/*
    $current_user = pnUserGetVar('uid');
    $sql = "INSERT INTO $pntable[todolist_projects] VALUES (1,'default','the default project',$current_user)";
    $dbconn->Execute($sql);

    $sql = "INSERT INTO $pntable[todolist_project_members] VALUES (1,$current_user)";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Table creation failed'));
        return false;
    }
*/
    // Set up an initial value for a module variable.
    // TODO: lowercasing these

    xarModSetVar('todolist', 'ACCESS_RESTRICTED', 0);
    xarModSetVar('todolist', 'BACKGROUND_COLOR', "#99ccff");
    xarModSetVar('todolist', 'DATEFORMAT', "1");
    xarModSetVar('todolist', 'DONE_COLOR', "#ccffff");
    xarModSetVar('todolist', 'HIGH_COLOR', "#ffff00");
    xarModSetVar('todolist', 'LOW_COLOR', "#66ccff");
    xarModSetVar('todolist', 'MAX_DONE', 10);
    xarModSetVar('todolist', 'MED_COLOR', "#ffcc66");
    xarModSetVar('todolist', 'MOST_IMPORTANT_COLOR', "#ffff99");
    xarModSetVar('todolist', 'MOST_IMPORTANT_DAYS', 3);
    xarModSetVar('todolist', 'REFRESH_MAIN', 600);
    xarModSetVar('todolist', 'SEND_MAILS', true);
    xarModSetVar('todolist', 'SHOW_EXTRA_ASTERISK', 1);
    xarModSetVar('todolist', 'SHOW_LINE_NUMBERS', true);
    xarModSetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE', true);
    xarModSetVar('todolist', 'SHOW_PRIORITY_IN_TABLE', true);
    xarModSetVar('todolist', 'TODO_HEADING', "Todolist");
    xarModSetVar('todolist', 'VERY_IMPORTANT_COLOR', "#ff3366");
    xarModSetVar('todolist', 'VERY_IMPORTANT_DAYS', 3);
    // New additions
    xarModSetVar('todolist', 'itemsperpage', 20);
    xarModSetVar('todolist', 'SupportShortURLs', 0);

    xarModSetVar('todolist', 'useModuleAlias',false);
    xarModSetVar('todolist', 'aliasname','');



// These should go to user Vars...

//    xarModSetVar('todolist','userpref','1;all;0;1');


    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     * TODO what to check upon?
     */

    xarRegisterMask('ViewTodolist', 'All', 'todolist', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadTodolist', 'All', 'todolist', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditTodolist', 'All', 'todolist', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddTodolist', 'All', 'todolist', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteTodolist', 'All', 'todolist', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminTodolist', 'All', 'todolist', 'Item', 'All:All:All', 'ACCESS_ADMIN');


    // Initialisation successful
    return true;
}

/**
 * upgrade the todolist module from an old version
 * This function can be called multiple times
 */
function todolist_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.9.13':
            // Code to upgrade from version 0.9.13 goes here
            xarModSetVar('todolist','userpref','1;all;0;1');

            $dbconn =& xarDBGetConn();;
            $todolist_users = pnConfigGetVar('prefix') . '_todolist_users';
            $sql = "DROP TABLE $todolist_users";
            $dbconn->Execute($sql);

            // Check for an error with the database code, and if so set an
            // appropriate error message and return
            if ($dbconn->ErrorNo() != 0) {
               // Report failed deletion attempt
               return false;
            }
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the todolist module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function todolist_delete()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $group_memberstable = $xartable['todolist_group_members'];
    $groupstable = $xartable['todolist_groups'];
    $notestable = $xartable['todolist_notes'];
    $project_memberstable = $xartable['todolist_project_members'];
    $projectstable = $xartable['todolist_projects'];
    $responsible_groupstable = $xartable['todolist_responsible_groups'];
    $responsible_personstable = $xartable['todolist_responsible_persons'];
    $todostable = $xartable['todolist_todos'];

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the tables */
     $result = $datadict->dropTable($group_memberstable);
     $result = $datadict->dropTable($groupstable);
     $result = $datadict->dropTable($notestable);
     $result = $datadict->dropTable($project_memberstable);
     $result = $datadict->dropTable($projectstable);
     $result = $datadict->dropTable($responsible_groupstable);
     $result = $datadict->dropTable($responsible_personstable);
     $result = $datadict->dropTable($todostable);

    // Delete any module variables
    xarModDelAllVars('todolist');

    /* Remove Masks and Instances
     */
    xarRemoveMasks('todolist');
    xarRemoveInstances('todolist');

    // Deletion successful
    return true;
}

?>