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
    $pntable = array();

    // Set name for Tracks table
    $tracks = pnConfigGetVar('prefix') . '_tracks';

    // Set the table name
    $pntable['tracks'] = $tracks;

    // Set the column names.  
    $pntable['tracks_column'] = 
    
    array('trackid'     => $tracks . '.pn_trackid',
          'trackname'   => $tracks . '.pn_trackname',
          'tracklead'   => $tracks . '.pn_tracklead',
	  'tracktext'   => $tracks . '.pn_tracktext',
	  'trackstatus' => $tracks . '.pn_trackstatus',
	  'trackcat'    => $tracks . '.pn_trackcat');

    // Set name for Tasks table
    $tasks = pnConfigGetVar('prefix') . '_tasks';

    // Set the table name
    $pntable['tasks'] = $tasks;

    // Set the column names.
    $pntable['tasks_column'] = 

    array('taskid'      => $tasks . '.pn_taskid',
          'trackid'     => $tasks . '.pn_trackid',
	  'tasktitle'   => $tasks . '.pn_tasktitle',
          'tasktext'    => $tasks . '.pn_tasktext',
          'taskstart'   => $tasks . '.pn_taskstart',
          'taskend'     => $tasks . '.pn_taskend',
          'tasklast'    => $tasks . '.pn_tasklast',
          'taskpercent' => $tasks . '.pn_taskpercent',
          'tasksteps'   => $tasks . '.pn_tasksteps',
	  'taskteam'    => $tasks . '.pn_taskteam');

    // Return the table information
    return $pntable;
}

?>
