<?php
// $Id: s.xarinit.php 1.2 02/12/01 14:28:07+01:00 marcel@hsdev.com $
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
// Current  Author of file: Curtis Nelson
// Purpose of file:  Initialisation functions for Dynamic Planning
// ----------------------------------------------------------------------

// initialise the module

function dynamic_planning_init()
{
    // Get datbase setup 
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // create table
    $dp_trackstable = $xartable['dp_tracks'];
    $dp_trackscolumn = &$xartable['dp_tracks_column'];

    $sql = "CREATE TABLE $dp_trackstable (
            $dp_trackscolumn[trackid] int(11) NOT NULL auto_increment,
            $dp_trackscolumn[trackname] text,
            $dp_trackscolumn[tracklead] text,
            $dp_trackscolumn[tracktext] text,
            $dp_trackscolumn[trackstatus] text,
            $dp_trackscolumn[trackcat] int(11),
            PRIMARY KEY(xar_trackid))";
    $dbconn->Execute($sql);

    // Check for an error 
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', "$dbconn->ErrorNo()");
        return false;
    }

    // Create the table
    $dp_taskstable = $xartable['dp_tasks'];
    $dp_taskscolumn = &$xartable['dp_tasks_column'];

    $sql = "CREATE TABLE $dp_taskstable (
            $dp_taskscolumn[taskid] int(11) NOT NULL auto_increment,
            $dp_taskscolumn[trackid] int(11) DEFAULT '0' NOT NULL,
            $dp_taskscolumn[tasktitle] varchar(80),
            $dp_taskscolumn[tasktext] text,
            $dp_taskscolumn[taskstart] date,
            $dp_taskscolumn[taskend] date,
            $dp_taskscolumn[tasklast] date,
            $dp_taskscolumn[taskpercent] int(11) DEFAULT '0' NOT NULL,
            $dp_taskscolumn[tasksteps] text,
            $dp_taskscolumn[taskteam] text,
            PRIMARY KEY(xar_taskid))";
    $dbconn->Execute($sql);

    // Check for an error
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Create tasks table failed'));
        return false;
    }


    // Set up an initial value for a module variable.  Note that all module
    // variables should be initialised with some value in this way rather
    // than just left blank, this helps the user-side code and means that
    // there doesn't need to be a check to see if the variable is set in
    // the rest of the code as it always will be
    xarModSetVar('dynamic_planning', 'bold', 0);
    xarModSetVar('dynamic_planning', 'itemsperpage', 10);

    // Initialisation successful
    return true;
}

/**
 * upgrade the template module from an old version
 * This function can be called multiple times
 */
function dynamic_planning_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
        case '1.0.0':
            break;
        default:
            // No Upgrade path yet 
            break;
    }

    // Update successful
    return true;
}

/**
 * delete the template module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function dynamic_planning_delete()
{
    // Get datbase setup 
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    // Set table names
    $dp_trackstable = $xartable['dp_tracks'];
    $dp_taskstable  = $xartable['dp_tasks'];
    
    // Drop the table 
    $sql = "DROP TABLE $dp_trackstable";
    $dbconn->Execute($sql);

    // Check for an error 
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
	pnSessionSetVar('errormsg', xarML('Delete tracks table failed'));
        return false;
    }

    // Drop the table 
    $sql = "DROP TABLE $dp_taskstable";
    $dbconn->Execute($sql);

    // Check for an error 
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
	pnSessionSetVar('errormsg', xarML('Delete tasks table failed'));
        return false;
    }

    // Delete any module variables
    xarModDelVar('dynamic_planning', 'itemsperpage');
    xarModDelVar('dynamic_planning', 'bold');

    // Deletion successful
    return true;
}

?>