<?php // $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Initialisation functions for todolist
// ----------------------------------------------------------------------

/**
 * initialise the todolist module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function todolist_init()
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $sql = "CREATE TABLE $pntable[todolist_group_members] (
            pn_group_id int(10) NOT NULL default '0',
            pn_member_id int(10) NOT NULL default '0',
            PRIMARY KEY (pn_group_id, pn_member_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_groups] (
            pn_id int(10) NOT NULL auto_increment,
            pn_group_name varchar(30) default NULL,
            pn_description varchar(200) default NULL,
            pn_group_leader int(11) default NULL,
            PRIMARY KEY  (pn_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_notes] (
            pn_todo_id int(10) unsigned NOT NULL default '0',
            pn_note_id int(10) unsigned NOT NULL auto_increment,
            pn_text text,
            pn_usernr int(10) default NULL,
            pn_date int(11) NOT NULL default '0',
            PRIMARY KEY  (pn_note_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_project_members] (
            pn_project_id int(10) NOT NULL default '0',
            pn_member_id int(10) NOT NULL default '0',
            PRIMARY KEY  (pn_project_id,pn_member_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_projects] (
            pn_id int(10) NOT NULL auto_increment,
            pn_project_name varchar(30) default NULL,
            pn_description varchar(200) default NULL,
            pn_project_leader int(11) NOT NULL default '0',
            PRIMARY KEY  (pn_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_responsible_groups] (
            pn_todo_id int(10) NOT NULL default '0',
            pn_group_id int(10) NOT NULL default '0',
            PRIMARY KEY  (pn_todo_id,pn_group_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_responsible_persons] (
            pn_todo_id int(10) NOT NULL default '0',
            pn_user_id int(10) NOT NULL default '0',
            PRIMARY KEY  (pn_todo_id,pn_user_id))";
    $dbconn->Execute($sql);

    $sql = "CREATE TABLE $pntable[todolist_todos] (
            pn_todo_id int(10) unsigned NOT NULL auto_increment,
            pn_project_id int(10) unsigned NOT NULL default '0',
            pn_todo_text varchar(255) NOT NULL default '',
            pn_todo_priority int(11) unsigned default NULL,
            pn_percentage_completed int(3) unsigned NOT NULL default '0',
            pn_created_by int(11) NOT NULL default '0',
            pn_due_date date default NULL,
            pn_date_created int(11) NOT NULL default '0',
            pn_date_changed int(11) NOT NULL default '0',
            pn_changed_by int(11) NOT NULL default '0',
            pn_status smallint(6) NOT NULL default '0',
            PRIMARY KEY  (pn_todo_id))";
    $dbconn->Execute($sql);

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

    // Set up an initial value for a module variable.  Note that all module
    // variables should be initialised with some value in this way rather
    // than just left blank, this helps the user-side code and means that
    // there doesn't need to be a check to see if the variable is set in
    // the rest of the code as it always will be

    pnModSetVar('todolist', 'ACCESS_RESTRICTED', 0);
    pnModSetVar('todolist', 'BACKGROUND_COLOR', "#99ccff");
    pnModSetVar('todolist', 'DATEFORMAT', "1");
    pnModSetVar('todolist', 'DONE_COLOR', "#ccffff");
    pnModSetVar('todolist', 'HIGH_COLOR', "#ffff00");
    pnModSetVar('todolist', 'LOW_COLOR', "#66ccff");
    pnModSetVar('todolist', 'MAX_DONE', 10);
    pnModSetVar('todolist', 'MED_COLOR', "#ffcc66");
    pnModSetVar('todolist', 'MOST_IMPORTANT_COLOR', "#ffff99");
    pnModSetVar('todolist', 'MOST_IMPORTANT_DAYS', 3);
    pnModSetVar('todolist', 'REFRESH_MAIN', 600);
    pnModSetVar('todolist', 'SEND_MAILS', true);
    pnModSetVar('todolist', 'SHOW_EXTRA_ASTERISK', 1);
    pnModSetVar('todolist', 'SHOW_LINE_NUMBERS', true);
    pnModSetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE', true);
    pnModSetVar('todolist', 'SHOW_PRIORITY_IN_TABLE', true);
    pnModSetVar('todolist', 'TODO_HEADING', "Todolist");
    pnModSetVar('todolist', 'VERY_IMPORTANT_COLOR', "#ff3366");
    pnModSetVar('todolist', 'VERY_IMPORTANT_DAYS', 3);
    pnModSetVar('todolist', 'ITEMS_PER_PAGE', 20);

    pnModSetVar('todolist','userpref','1;all;0;1');

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
            pnModSetVar('todolist','userpref','1;all;0;1');
            
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
    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    // return arrays but we handle them differently.  For pnDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For pnDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // Drop the table - for such a simple command the advantages of separating
    // out the SQL statement from the Execute() command are minimal, but as
    // this has been done elsewhere it makes sense to stick to a single method
    $sql = "DROP TABLE $pntable[todolist_group_members]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_groups]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_notes]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_project_members]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_projects]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_responsible_groups]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_responsible_persons]";
    $dbconn->Execute($sql);
    $sql = "DROP TABLE $pntable[todolist_todos]";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    // Delete any module variables
    pnModDelVar('todolist', 'ACCESS_RESTRICTED');
    pnModDelVar('todolist', 'BACKGROUND_COLOR');
    pnModDelVar('todolist', 'DATEFORMAT');
    pnModDelVar('todolist', 'DONE_COLOR');
    pnModDelVar('todolist', 'HIGH_COLOR');
    pnModDelVar('todolist', 'LOW_COLOR');
    pnModDelVar('todolist', 'MAX_DONE');
    pnModDelVar('todolist', 'MED_COLOR');
    pnModDelVar('todolist', 'MOST_IMPORTANT_COLOR');
    pnModDelVar('todolist', 'MOST_IMPORTANT_DAYS');
    pnModDelVar('todolist', 'REFRESH_MAIN');
    pnModDelVar('todolist', 'SEND_MAILS');
    pnModDelVar('todolist', 'SHOW_EXTRA_ASTERISK');
    pnModDelVar('todolist', 'SHOW_LINE_NUMBERS');
    pnModDelVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE');
    pnModDelVar('todolist', 'SHOW_PRIORITY_IN_TABLE');
    pnModDelVar('todolist', 'TODO_HEADING');
    pnModDelVar('todolist', 'VERY_IMPORTANT_COLOR');
    pnModDelVar('todolist', 'VERY_IMPORTANT_DAYS');
    pnModDelVar('todolist', 'ITEMS_PER_PAGE');

    pnModDelVar('todolist','userpref');

    // Deletion successful
    return true;
}

?>