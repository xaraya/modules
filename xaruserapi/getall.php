<?php
/**
 * get all ping urls
 * @returns array
 * @return array of links, or false on failure
 */
function ping_userapi_getall($args)
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
    // Security Check
    if(!xarSecurityCheck('Readping')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pingtable = $xartable['ping'];
    // Get links
    $query = "SELECT xar_id,
                     xar_url,
                     xar_method
            FROM $pingtable";
    $query .= " ORDER BY xar_id";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $url, $method) = $result->fields;
        if (xarSecurityCheck('Readping')) {
            $links[] = array('id'       => $id,
                             'url'      => $url,
                             'method'   => $method);
        }
    }
    $result->Close();
    return $links;
}
?>