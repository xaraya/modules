<?php
/**
 * get all transactions
 * @returns array
 * @return array of links, or false on failure
 */
function pmember_userapi_getall($args)
{
    extract($args);
    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    $links = array();

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['pmember'];
    $query = "SELECT xar_uid,
                     xar_subscribed,
                     xar_expires
            FROM $table";
    $query .= " ORDER BY xar_uid";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($uid, $subscribed, $expires) = $result->fields;
        if (xarSecurityCheck('ViewPmember')) {
            $links[] = array('uid'           => $uid,
                             'subscribed'    => $subscribed,
                             'expires'       => $expires);
        }
    }
    $result->Close();
    return $links;
}
?>