<?php
// $Id: s.xaradminapi.php 1.3 02/12/01 14:27:27+01:00 marcel@hsdev.com $
// ----------------------------------------------------------------------
// Dynamic Planning Module
// Copyright (C) 2002 by Curtis Nelson
// http://www.liminis.com/~curtis/
// ----------------------------------------------------------------------
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
// Purpose of file:  administration API
// ----------------------------------------------------------------------

/**
 * create a new track
 * @param $args['name'] name of the item
 * @param $args['number'] number of the item
 * @returns int
 * @return template item ID on success, false on failure
 */
function dynamic_planning_adminapi_create($args)
{

    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of PostNuke
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($trackname)) ||
        (!isset($tracktext))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check - important to do this as early on as possible to 
    // avoid potential security holes or just too much wasted processing
    if (!pnSecAuthAction(0, 'dynamic_planning::Item', "$trackname::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', _TEMPLATENOAUTH);
        return false;
    }

    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    // return arrays but we handle them differently.  For pnDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For pnDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $trackstable = $pntable['dp_tracks'];
    $trackscolumn = &$pntable['dp_tracks_column'];

    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
    $nextId = $dbconn->GenId($trackstable);

    // Add item - the formatting here is not mandatory, but it does make
    // the SQL statement relatively easy to read.  Also, separating out
    // the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $sql = "INSERT INTO $trackstable (
              $trackscolumn[trackid],
              $trackscolumn[trackname],
	      $trackscolumn[tracklead],
              $trackscolumn[tracktext],
	      $trackscolumn[trackstatus],
	      $trackscolumn[trackcat])
            VALUES (
              $nextId,
              '" . pnVarPrepForStore($trackname) . "',
	      NULL,
              '" . pnvarPrepForStore($tracktext) . "',
	      NULL,
	      NULL)";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }

    // Get the ID of the item that we inserted.  It is possible, although
    // very unlikely, that this is different from $nextId as obtained
    // above, but it is better to be safe than sorry in this situation
    $trackid = $dbconn->PO_Insert_ID($trackstable, $trackscolumn['trackid']);

    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'tid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
    pnModCallHooks('item', 'create', $trackid, 'trackid');

    // Return the id of the newly created item to the calling process
    return $trackid;
}

/**
 * delete a track
 * @param $args['tid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 */
function dynamic_planning_adminapi_delete($args)
{
    // Get arguments from argument array - all arguments to this function
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($trackid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    
    $output = new pnHTML();

    // Load API.  Note that this is loading the user API in addition to
    // the administration API, that is because the user API contains
    // the function to obtain item information which is the first thing
    // that we need to do.  If the API fails to load an appropriate error
    // message is posted and the function returns
    if (!pnModAPILoad('dynamic_planning', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
    }

    // The user API function is called.  This takes the item ID which
    // we obtained from the input and gets us the information on the
    // appropriate item.  If the item does not exist we post an appropriate
    // message and return
    $item = pnModAPIFunc('dynamic_planning',
            'user',
            'getall'
            );

    if ($item == false) {
        $output->Text(xarML('No such item'));
        return $output->GetOutput();
    }

    // Security check - important to do this as early on as possible to 
    // avoid potential security holes or just too much wasted processing.
    // However, in this case we had to wait until we could obtain the item
    // name to complete the instance information so this is the first
    // chance we get to do the check
    if (!pnSecAuthAction(0, 'dynamic_planning::', "::", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Planning module'));
        return false;
    }

    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $trackstable = $pntable['dp_tracks'];
    $trackscolumn = &$pntable['dp_tracks_column'];

    // Delete the item - the formatting here is not mandatory, but it does
    // make the SQL statement relatively easy to read.  Also, separating
    // out the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $sql = "DELETE FROM $trackstable
            WHERE $trackscolumn[trackid] = '" . pnVarPrepForStore($trackid) . "'";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    pnModCallHooks('track', 'delete', $trackid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * update a track
 * @param $args['tid'] the ID of the item
 * @param $args['name'] the new name of the item
 * @param $args['number'] the new number of the item
 */
function dynamic_planning_adminapi_update($args)
{
    // Get arguments from argument array - all arguments to this function
    extract($args);

    // Argument check - make sure that all required arguments are present,
    if ((!isset($trackid)) ||
        (!isset($trackname)) ||
        (!isset($tracktext))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Load API.  Note that this is loading the user API in addition to
    if (!pnModAPILoad('dynamic_planning', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
    }

    // The user API function is called.  This takes the item ID which
    $track = pnModAPIFunc('dynamic_planning',
            'user',
            'gettrack',
            array('trackid' => $trackid));

    if ($track == false) {
        $output->Text(xarML('No such item'));
        return $output->GetOutput();
    }

    // Security check - important to do this as early on as possible to 
    if (!pnSecAuthAction(0, 'dynamic_planning::', "$track[trackname]::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Planning module'));
        return false;
    }

    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you
    $trackstable = $pntable['dp_tracks'];
    $trackscolumn = &$pntable['dp_tracks_column'];

    // Update the item - the formatting here is not mandatory, but it does
    $sql = "UPDATE $trackstable
            SET $trackscolumn[trackname] = '" . pnVarPrepForStore($trackname) . "',
                $trackscolumn[tracktext] = '" . pnVarPrepForStore($tracktext) . "'
            WHERE $trackscolumn[trackid] = " . pnVarPrepForStore($trackid);
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * update a task
 * @param $args['tid'] the ID of the item
 * @param $args['name'] the new name of the item
 * @param $args['number'] the new number of the item
 */
function dynamic_planning_adminapi_updatetask($args)
{
    // Get arguments from argument array - all arguments to this function
    extract($args);

    // Argument check - make sure that all required arguments are present,
    if ((!isset($taskid)) ||
        (!isset($tasktitle)) ||
	(!isset($tasktext)) ||
	(!isset($taskstart)) ||
	(!isset($taskend)) ||
	(!isset($taskpercent)) ||
	(!isset($tasksteps)) ||
        (!isset($taskteam))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Load API.  Note that this is loading the user API in addition to
    if (!pnModAPILoad('dynamic_planning', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
    }

    // The user API function is called.  This takes the item ID which
    $task = pnModAPIFunc('dynamic_planning',
            'user',
            'gettask',
            array('taskid' => $taskid));

    if ($task == false) {
        $output->Text(xarML('No such item'));
        return $output->GetOutput();
    }

    // Security check - important to do this as early on as possible to
    if (!pnSecAuthAction(0, 'dynamic_planning::', "$track[trackname]::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Planning module'));
        return false;
    }

    // Get datbase setup - note that both pnDBGetConn() and pnDBGetTables()
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you
    $taskstable = $pntable['dp_tasks'];
    $taskscolumn = &$pntable['dp_tasks_column'];

    // Update the item - the formatting here is not mandatory, but it does
    $sql = "UPDATE $taskstable
            SET $taskscolumn[tasktitle] = '" . pnVarPrepForStore($tasktitle) . "',
                $taskscolumn[tasktext] = '" . pnVarPrepForStore($tasktext) . "',
		$taskscolumn[taskstart] = '" . pnVarPrepForStore($taskstart) . "',
		$taskscolumn[taskend] = '" . pnVarPrepForStore($taskend) . "',
		$taskscolumn[taskpercent] = " . pnVarPrepForStore($taskpercent) . ",
		$taskscolumn[tasksteps] = '" . pnVarPrepForStore($tasksteps) . "',
		$taskscolumn[taskteam] = '" . pnVarPrepForStore($taskteam) . "'
            WHERE $taskscolumn[taskid] = " . pnVarPrepForStore($taskid);
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>