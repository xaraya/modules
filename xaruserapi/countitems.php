<?php

function xtasks_userapi_countitems($args)
{
	extract($args);
	
	if(empty($parentid)) $parentid = 0;
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtasks_table = $xartable['xtasks'];
    $xtasks_column = &$xartable['xtasks_column'];

    $sql = "SELECT COUNT(1)
            FROM $xtasks_table
			WHERE parentid = $parentid";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}
?>