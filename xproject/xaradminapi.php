<?php
/**
 * 
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/

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

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xproject_adminapi_getmenulinks()
{

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'main'),
                              'title' => xarML('The overview of this module and its functions'),
                              'label' => xarML('Overview'));
    }

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADD)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a new project'),
                              'label' => xarML('New Project'));
    }

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_READ)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'view'),
                              'title' => xarML('List of current projects'),
                              'label' => xarML('View Projects'));

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'search'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Projects'));
    }

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADMIN)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the Admin Panels'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>