<?php
/**
 * Count the items
 *
 */
function tasks_userapi_countitems($args)
{
	extract($args);
	
	if(empty($parentid)) $parentid = "0";
	
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $taskstable = $xartable['tasks'];
    $taskscolumn = &$xartable['tasks_column'];

    $sql = "SELECT COUNT(1)
            FROM $taskstable
			WHERE xar_parentid = $parentid";

    if(!empty($statustype)) {
        switch($statustype) {
		case "open":
			$sql .= " AND xar_status = 0";
			break;
		case "closed":
			$sql .= " AND xar_status = 1";
			break;
        } // ELSE GET ALL
    }
    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}

?>