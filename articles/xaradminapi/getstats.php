<?php

/**
 * count number of items depending on additional module criteria
 *
 * @returns array
 * @return number of items
 */
function articles_adminapi_getstats($args)
{
    // Database information
    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();

    $query = 'SELECT xar_pubtypeid, xar_status, xar_authorid, COUNT(*)
              FROM ' . $xartables['articles'] . '
              GROUP BY xar_pubtypeid, xar_status, xar_authorid';

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $stats = array();
    while (!$result->EOF) {
        list($ptid,$status,$authorid,$count) = $result->fields;
        $stats[$ptid][$status][$authorid] = $count;
        $result->MoveNext();
    }
    $result->Close();

    return $stats;
}

?>
