<?php
/**
 * get all Ephemerids
 *
 * @author the Ephemerids module development team
 * @param numitems the number of items to retrieve (default -1 = all)
 * @param startnum start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ephemerids_userapi_getalltoday()
{
    $items = array();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];
    $today = getdate();
    $eday = $today['mday'];
    $emonth = $today['mon'];

    $query = "SELECT xar_eid,
                     xar_did,
                     xar_mid, 
                     xar_yid,
                     xar_content,
                     xar_elanguage
            FROM $ephemtable
            WHERE xar_did = ? AND xar_mid = ? ";
    $bindvars = array($eday, $emonth);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Put items into result array. 
    for (; !$result->EOF; $result->MoveNext()) {
        list($eid, $did, $mid, $yid, $content, $elanguage) = $result->fields;
        if (xarSecurityCheck('OverviewEphemerids', 0)) {
            $items[] = array(
                  'did' => $did,
                  'mid' => $mid,
                  'yid' => $yid,
                  'content' => $content);
        }
    }
    $result->Close();
    // Return the items
    return $items;
}
?>