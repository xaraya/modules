<?php
function netquery_userapi_getlinks($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) {
        $startnum = 1;
    }
    if ((!isset($numitems)) || (!is_numeric($numitems))) {
        $numitems = -1;
    }
    $links = array();
    if (!xarSecurityCheck('OverviewNetquery')) {
        return $links;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT * FROM $WhoisTable ORDER BY whois_ext";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($whois_id, $whois_ext, $whois_server) = $result->fields;
        $links[] = array('whois_id'      => $whois_id,
                         'whois_ext'     => $whois_ext,
                         'whois_server'  => $whois_server);
    }
    $result->Close();
    return $links;
}
?>