<?php

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function xarbb_userapi_counttopics($args)
{

    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    $query = "SELECT COUNT(1)
              FROM $xbbforumstable            
              WHERE xar_fid = $fid";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>