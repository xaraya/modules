<?php

function xproject_userapi_countitems($args)
{
	extract($args);
	
	if(empty($parentid)) $parentid = 0;
	
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xproject'];
    $taskcolumn = &$xartable['xproject_column'];

    $sql = "SELECT COUNT(1)
            FROM $xprojecttable
			WHERE $taskcolumn[parentid] = $parentid";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}
?>