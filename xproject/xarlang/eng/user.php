<?php // $Id: user.php,v 1.9 2002/03/12 10:32:34 voll Exp $
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phxaruke.org/
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
// Original Author of file: Chad Kraeft
// Purpose of file:  Language defines for xaruser.php
// ----------------------------------------------------------------------
define('_XPROJECT', 'Task');
define('_XPROJECTITEMFAILED', 'No (sub)tasks available for this Project/Group/Task');
define('_XPROJECTNAME', 'Example task name');
define('_XPROJECTNUMBER', 'Example task number');
define('_XPROJECTVIEW', 'View Tasks');
define('_XPROJECTNEW', 'New Task');
define('_XPROJECTEDITUSERCONFIG', 'Preferences');
define('_XPROJECTEDITCONFIG', 'Config');
define('_XPROJECTVIEWUSERS', ' Members');
define('_XPROJECTVIEWGROUPS', 'View Teams');
define('_XPROJECTEMAILNOTIFY', 'Email notification on task change');
define('_XPROJECTPRIMARYTASK', 'Primary project / tasklist');
define('_XPROJECTMYTASKS', 'Only show tasks assigned to me');
define('_XPROJECTMODIFYCONFIG', 'Modify Tasks Preferences');
define('_XPROJECTSEARCH', 'Search Tasks');
define('_XPROJECTID', 'task id');
define('_PARENTID', 'parent id');
define('_CREATOR', 'Task originator');
define('_OWNER', 'Current task owner');
define('_ASSIGNER', 'Assigned by');
define('_TEAM', 'Team');
define('_PRIORITY', 'Priority');
define('_STATUS', 'Status');
define('_PRIVATE', 'Private');
define('_XPROJECTNO', 'No');
define('_XPROJECTYES', 'Yes');
define('_DATESUBMIT', 'Submitted on');
define('_DATEAPPROVE', 'Approved on');
define('_DATEMODIFIED', 'Last Modified');
define('_DATESTART', 'Start date');
define('_DATEEND', 'End date');
define('_DATEACTSTART', 'Actual start date');
define('_DATEACTEND', 'Actual end date');
define('_HOURSPLANNED', 'Hours planned');
define('_HOURSSPENT', 'Hours spent');
define('_HOURSTOGO', 'Hours remaining');
define('_XPROJECTNONE', 'No tasks listed');
define('_MASTERTASK', 'Project Master');
define('_XPROJECTPROJECTSVIEW', 'Projects');
define('_RATE', 'Rate');
define('_GROUPHEADER', ' Org');
define('_PROJECTHEADER', ' Project');
define('_PARENTHEADER', 'Subtask of ');
define('_XPROJECT_LOGIN_USER_UNKNOWN', 'You must be logged in to access this module');
if (!defined('_XPROJECTNOAUTH')) {
	define('_XPROJECTNOAUTH','Not authorised to access Tasks module');
}
?>