<?php
// $Id: s.xartables.php 1.2 02/12/01 14:29:27+01:00 marcel@hsdev.com $
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
// Purpose of file:  Table information for Dynamic Planning module
// ----------------------------------------------------------------------

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the table information
 */
function dynamic_planning_xartables()
{
    // Initialise table array
    $xartable = array();

    // Set name for Tracks table
    $dp_tracks = xarDBGetSiteTablePrefix() . '_dp_tracks';

    // Set the table name
    $xartable['dp_tracks'] = $dp_tracks;

    // Set the column names.  
    $xartable['dp_tracks_column'] = 
    
    array('trackid'     => $dp_tracks . '.xar_trackid',
          'trackname'   => $dp_tracks . '.xar_trackname',
          'tracklead'   => $dp_tracks . '.xar_tracklead',
          'tracktext'   => $dp_tracks . '.xar_tracktext',
          'trackstatus' => $dp_tracks . '.xar_trackstatus',
          'trackcat'    => $dp_tracks . '.xar_trackcat');

    // Set name for Tasks table
    $dp_tasks = xarConfigGetVar('prefix') . '_dp_tasks';

    // Set the table name
    $xartable['dp_tasks'] = $dp_tasks;

    // Set the column names.
    $xartable['dp_tasks_column'] = 

    array('taskid'      => $dp_tasks . '.xar_taskid',
          'trackid'     => $dp_tasks . '.xar_trackid',
          'tasktitle'   => $dp_tasks . '.xar_tasktitle',
          'tasktext'    => $dp_tasks . '.xar_tasktext',
          'taskstart'   => $dp_tasks . '.xar_taskstart',
          'taskend'     => $dp_tasks . '.xar_taskend',
          'tasklast'    => $dp_tasks . '.xar_tasklast',
          'taskpercent' => $dp_tasks . '.xar_taskpercent',
          'tasksteps'   => $dp_tasks . '.xar_tasksteps',
          'taskteam'    => $dp_tasks . '.xar_taskteam');

    // Return the table information
    return $xartable;
}

?>