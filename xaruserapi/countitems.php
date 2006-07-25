<?php

function accessmethods_userapi_countitems($args)
{
	extract($args);
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $accessmethodstable = $xartable['accessmethods'];

    $sql = "SELECT COUNT(1)
            FROM $accessmethodstable
			WHERE 1";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>
