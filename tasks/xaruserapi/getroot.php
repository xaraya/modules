<?php
/**
 * Get the root task
 *
 */
function tasks_userapi_getroot($args)
{
	extract($args);
	
	if (!isset($id) || !is_numeric($id)) {
        //xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>tasks_userapi_getroot: ' . _TASKS_MODARGSERROR);
        return false;
    }
	
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];
    $taskscolumn = &$xartable['tasks_columns'];
	$rootid = $id;
	while($rootid != 0) {
		$sql = "SELECT $taskscolumn[id],
					$taskscolumn[parentid]
				FROM $taskstable
				WHERE xar_id = $rootid";
		$result =& $dbconn->Execute($sql);
        if (!$result) return;

		list($parentid, $rootid) = $result->fields;
	}
	
    $result->Close();

    return $parentid;
}

?>