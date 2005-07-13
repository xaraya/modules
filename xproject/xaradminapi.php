<?php
//
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
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
// Purpose of file:  task administration API
// ----------------------------------------------------------------------

function xproject_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($sendmails) || $sendmails == 0) {
        $invalid[] = 'sendmails';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
	
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::", ACCESS_ADD)) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    $nextId = $dbconn->GenId($xprojecttable);

    $sql = "INSERT INTO $xprojecttable (
              xar_projectid,
              xar_name,
			  xar_description,
			  xar_usedatefields,
			  xar_usehoursfields,
              xar_usefreqfields,
              xar_allowprivate,
              xar_importantdays,
			  xar_criticaldays, 
			  xar_sendmailfreq, 
			  xar_billable)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($description) . "',
              " . ($displaydates ? $displaydates : "NULL") . ",
              " . ($displayhours ? $displayhours : "NULL") . ",
              " . ($displayfreq ? $displayfreq : "NULL") . ",
              " . ($private ? $private : "NULL") . ",
              " . $importantdays . ",
              " . $criticaldays . ",
              " . $sendmails . ",
              " . ($billable ? $billable : "NULL") . ")";			  

// PRIVATE INITIALLY SET BASED ON USER PREFERENCE

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $projectid = $dbconn->PO_Insert_ID($xprojecttable, 'xar_projectid');

    $item = $args;
    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    xarModCallHooks('item', 'create', $projectid, $item);

    return $projectid;
}

function xproject_adminapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($sendmailfreq) || $sendmailfreq == 0) {
        $invalid[] = 'sendmails';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'update', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

	if (!xarModAPILoad('xproject', 'user')) return;

    $item = xarModAPIFunc('xproject',
						'user',
						'get',
						array('projectid' => $projectid));
			
	if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$item[name]::$projectid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
    if (!xarSecAuthAction(0, 'xproject::Project', "$name::$projectid", ACCESS_EDIT)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
		
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    $sql = "UPDATE $xprojecttable
              SET xar_name = '" . xarVarPrepForStore($name) . "',
				  xar_description = '" . xarVarPrepForStore($description) . "',
				  xar_usedatefields = " . ($displaydates ? $displaydates : "NULL") . ",
				  xar_usehoursfields = " . ($displayhours ? $displayhours : "NULL") . ",
				  xar_usefreqfields = " . ($displayfreq ? $displayfreq : "NULL") . ",
				  xar_allowprivate = " . ($private ? $private : "NULL") . ",
				  xar_importantdays = " . $importantdays . ",
				  xar_criticaldays = " . $criticaldays . ", 
				  xar_sendmailfreq = " . $sendmailfreq . ", 
				  xar_billable = " . ($billable ? $billable : "NULL") . "
			WHERE xar_projectid = $projectid";

    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    $item['name'] = $name;
    $item['description'] = $description;
    xarModCallHooks('item', 'update', $projectid, $item);

    return true;
}

function xproject_adminapi_delete($args)
{
    extract($args);

    if (!isset($projectid) || !is_numeric($projectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'admin', 'delete', 'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPILoad('xproject', 'user')) return;

    // does it exist ?
    $project = xarModAPIFunc('xproject',
							'user',
							'get',
							array('projectid' => $projectid));

    if (!isset($project) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$project[name]::$projectid", ACCESS_DELETE)) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];

    // does it have children ?
    $sql = "DELETE FROM $xprojecttable
            WHERE xar_projectid = " . xarVarPrepForStore($projectid);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item['module'] = 'xproject';
    $item['itemid'] = $projectid;
    xarModCallHooks('item', 'delete', $projectid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

function xproject_adminapi_migrate($args)
{
    extract($args);

    $invalid = array();
	if (!isset($targetfocus)) $targetfocus = 0;

	if (!xarModAPILoad('xproject', 'user')) return;

    $item = xarModAPIFunc('xproject',
						'user',
						'get',
						array('projectid' => $projectid));
			
	if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    if (!xarSecAuthAction(0, 'xproject::Project', "$item[name]::$projectid", ACCESS_MODERATE)) {
        $msg = xarML('Not authorized to edit #(1) item #(2)',
                    'xproject', xarVarPrepForStore($projectid));
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $taskstable = $xartable['xproject_tasks'];
	if(is_array($taskfocus)) {
		foreach($taskfocus as $targetid => $focus) {
			if($focus) $targetfocus = $targetid;
		}
	}
		
	$affectedtasks = array();
	if(is_array($taskcheck)) {
		foreach($taskcheck as $affectedid => $check) {
			if($affectedid != $targetfocus) $affectedtasks[] = $affectedid;
		}
	}
	
	if($targetfocus > 0) {
		// WTF WE'RE GONNA TRY TO DO HERE:
		//
		// - CASE OUT EACH OF FOUR POSSIBLE REASONS WE'RE HERE
		// - 1 => Migrate selected tasks under taskfocus (taskfocus[any] = 1)
		$sql = "UPDATE $taskstable SET xar_parentid = $targetfocus WHERE xar_taskid IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			$msg = xarMLByKey('DATABASE_ERROR', $sql);
			xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
						   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
			return;
		}
		
		return $targetfocus;

	} elseif($taskoption == 1) {
		// - 2 => Surface selected tasks to current task's parentid (taskoption = 1)
		// UH, THERE IS NO PARENTID PASSED
		$sql = "UPDATE $taskstable SET xar_parentid = " . ($item['parentid'] ? $item['parentid'] : "0") . " WHERE xar_taskid IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			$msg = xarMLByKey('DATABASE_ERROR', $sql);
			xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
						   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
			return;
		}
		
		return $taskid;
	
	} elseif($taskoption == 2) {
		// - 3 => Delete task and all subtasks (taskoption = 2)
		$resultlist = array();
		$resultlist[] = $affectedtasks;
		$selectedids = $affectedtasks;
		$numtasks = count($affectedtasks);
		while($numtasks > 0) {
			$sql = "SELECT xar_taskid FROM $taskstable WHERE xar_parentid IN (" . implode(",",$selectedids) . ")";

			$result = $dbconn->SelectLimit($sql, -1, 0);
			
			if ($dbconn->ErrorNo() != 0) {
				$msg = xarMLByKey('DATABASE_ERROR', $sql);
				xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
							   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
				return;
			}
			
			$selectedids = array();
			for (; !$result->EOF; $result->MoveNext()) {
				list($selectedid) = $result->fields;
				$selectedids[] = $selectedid;
			}
			$resultlist[] = $selectedids;
			$numtasks = count($selectedids);
		}

		foreach($resultlist as $tasklist) {
			$sql = "DELETE FROM $taskstable WHERE xar_taskid IN (" . implode(",",$tasklist) . ")";

			$dbconn->Execute($sql);
		
			if ($dbconn->ErrorNo() != 0) {
				$msg = xarMLByKey('DATABASE_ERROR', $sql);
				xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
							   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
				return;
			}
		}
		
		return $taskid;
		
	} elseif($taskoption == 3) {
		// - 4 => Delete task, but surface children under current task
		// WHICH SHOULD GO FIRST?
		// ? IF UPDATE FAILS FIRST, ERRMSG AND DO NOT DEL
		// ? IF DEL FAILS FIRST, CONTINUE W/UPDATE
		// IN SECOND SCENARIO, UNSUCCESSFUL UPDATES BECOME ORPHANS
		// HANDLE THAT AS PREVIOUSLY NOTED
		$sql = "UPDATE $taskstable SET xar_parentid = $taskid WHERE xar_parentid IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			$msg = xarMLByKey('DATABASE_ERROR', $sql);
			xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
						   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
			return;
		}

		$sql = "DELETE FROM $taskstable WHERE xar_taskid IN (" . implode(",",$affectedtasks) . ")";

		$dbconn->Execute($sql);
	
		if ($dbconn->ErrorNo() != 0) {
			$msg = xarMLByKey('DATABASE_ERROR', $sql);
			xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
						   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
			return;
		}
		
		return $taskid;
		
	} else $sql = "(no query)";
	//
	// EXPECTED ISSUES:
	// * Deletion of subtasks must be recursive/iterative 
	// (resolved by creating an array*array of taskid lists to use with an "IN" statement recursively)
	// everything else looks pretty cake, yeah?
	//
	///////////////////////////////////

    return $taskid;
}
?>