<?php
// $Id: s.xaruserapi.php 1.3 02/12/01 14:26:47+01:00 marcel@hsdev.com $
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
// Purpose of file:  Template user API
// ----------------------------------------------------------------------

/**
 * get all tracks
 * @returns array
 * @return array of items, or false on failure
 */
function dynamic_planning_userapi_getall($args)
{
    //
    extract($args);

    $items = array();

    // Security check - important to do this as early on as possible to 
    if (!pnSecAuthAction(0, 'dynamic_planning::', '::', ACCESS_READ)) {
        return $items;
    }

    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $trackstable = $pntable['dp_tracks'];
    $trackscolumn = &$pntable['dp_tracks_column'];

    // Get items - the formatting here is not mandatory, but it does make the
    $sql = "SELECT $trackscolumn[trackid],
                   $trackscolumn[trackname],
                   $trackscolumn[tracklead],
		   $trackscolumn[tracktext],
		   $trackscolumn[trackstatus]
            FROM $trackstable
            ORDER BY $trackscolumn[trackname]";
    $result = $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Items load failed'));
        return false;
    }

    // Put items into result array.  Note that each item is checked
    // individually to ensure that the user is allowed access to it before it
    // is added to the results array
    for (; !$result->EOF; $result->MoveNext()) {
        list($trackid, $trackname,$tracklead,$tracktext,$trackstatus) = $result->fields;
        $items[] = array('trackid' => $trackid,
                         'trackname' => $trackname,
                         'tracklead' => $tracklead,
			 'tracktext' => $tracktext,
			 'trackstatus' => $trackstatus);
        
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the items
    return $items;
}

/**
 * get all tasks for track
 * @param $args['trackid'] id of track to get tasks for
 * @returns array
 * @return item array, or false on failure
 */
function dynamic_planning_userapi_get($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other places
    // such as the environment is not allowed, as that makes assumptions that
    // will not hold in future versions of PostNuke
    extract($args);

    // Argument check - make sure that all required arguments are present, if
    // not then set an appropriate error message and return
    if (!isset($trackid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $taskstable = $pntable['dp_tasks'];
    $taskscolumn = &$pntable['dp_tasks_column'];

    // Get item - the formatting here is not mandatory, but it does make the
    $sql = "SELECT $taskscolumn[taskid],
                   $taskscolumn[tasktitle],
                   $taskscolumn[tasktext],
		   $taskscolumn[taskstart],
		   $taskscolumn[taskend],
		   $taskscolumn[tasklast],
		   $taskscolumn[taskpercent]
            FROM $taskstable
            WHERE $taskscolumn[trackid] = " . pnVarPrepForStore($trackid);
    $result = $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return false;
    }

    // Obtain the item information from the result set
    for (; !$result->EOF; $result->MoveNext()) {
    list($taskid,$tasktitle,$tasktext,$taskstart,$taskend,$tasklast,$taskpercent) = $result->fields;

    // Create the item array
    $item[] = array('taskid' => $taskid,
                    'tasktitle' => $tasktitle,
                    'tasktext' => $tasktext,
		    'taskstart' => $taskstart,
		    'taskend' => $taskend,
		    'tasklast' => $tasklast,
		    'taskpercent' => $taskpercent);
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
		
    // Return the item array
    return $item;
}

/**
 * get track by id
 * 
 */

function dynamic_planning_userapi_gettrack($args)
{  
    extract($args);

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $trackstable = $pntable['dp_tracks'];
    $trackscolumn = &$pntable['dp_tracks_column'];

    $sql = "SELECT $trackscolumn[trackname],
                   $trackscolumn[tracklead],
		   $trackscolumn[tracktext],
		   $trackscolumn[trackstatus],
		   $trackscolumn[trackcat]
            FROM $trackstable
	    WHERE $trackscolumn[trackid] = $trackid";
    $result = $dbconn->Execute($sql);

    list($trackname, $tracklead, $tracktext, $trackstatus, $trackcat) = $result->fields;

    $result->Close();

    $item = array('trackid'   => $trackid,
                  'trackname' => $trackname,
                  'tracklead' => $tracklead,
		  'tracktext' => $tracktext,
		  'trackstatus' => $trackstatus,
		  'trackcat'   => $trackcat) ;
    return $item;
}

/**
 * get task by id
 *
 */

function dynamic_planning_userapi_gettask($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $taskstable = $pntable['dp_tasks'];
    $taskscolumn = &$pntable['dp_tasks_column'];

    $sql = "SELECT $taskscolumn[trackid],
                   $taskscolumn[tasktitle],
                   $taskscolumn[tasktext],
                   $taskscolumn[taskstart],
                   $taskscolumn[taskend],
                   $taskscolumn[tasklast],
                   $taskscolumn[taskpercent],
		   $taskscolumn[tasksteps],
		   $taskscolumn[taskteam]
            FROM $taskstable
            WHERE $taskscolumn[taskid] = $taskid";
    $result = $dbconn->Execute($sql);

    list($trackid, $tasktitle, $tasktext, $taskstart, $taskend, $tasklast, $taskpercent, $tasksteps, $taskteam) = $result->fields;

    $result->Close();

    $item = array('taskid'   => $taskid,
                  'trackid' => $trackid,
                  'tasktitle' => $tasktitle,
                  'tasktext' => $tasktext,
                  'taskstart' => $taskstart,
                  'taskend' => $taskend,
                  'tasklast'   => $tasklast,
		  'taskpercent' => $taskpercent,
		  'tasksteps' => $tasksteps,
		  'taskteam' => $taskteam) ;
    return $item;
}


    
/**
 * utility function to count the number of items held by this module
 * @returns integer
 * @return number of items held by this module
 */
function template_userapi_countitems()
{
    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    // return arrays but we handle them differently.  For pnDBGetConn() we
    // currently just want the first item, which is the official database
    // handle.  For pnDBGetTables() we want to keep the entire tables array
    // together for easy reference later on
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $templatetable = $pntable['dp_template'];
    $templatecolumn = &$pntable['dp_template_column'];

    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $sql = "SELECT COUNT(1)
            FROM $templatetable";
    $result = $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    // Obtain the number of items
    list($numitems) = $result->fields;

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the number of items
    return $numitems;
}

?>