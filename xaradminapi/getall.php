<?php
/**
 * get all whois lookup links
 */

function netquery_adminapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = -1;
    }

    $links = array();

    if (!xarSecurityCheck('OverviewNetquery')) {
        return $links;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $query = "SELECT whois_id,
                     whois_ext,
                     whois_server
            FROM $WhoisTable
            ORDER BY whois_ext";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($whois_id, $whois_ext, $whois_server) = $result->fields;
        if (xarSecurityCheck('OverviewNetquery', 0)) {
            $links[] = array('whois_id'      => $whois_id,
                             'whois_ext'     => $whois_ext,
                             'whois_server'  => $whois_server);
        }
    }

    $result->Close();

    return $links;
}
?>
