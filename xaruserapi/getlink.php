<?php
function netquery_userapi_getlink($args)
{
    extract($args);
    if (!isset($whois_ext)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT * FROM $WhoisTable WHERE whois_ext = ?";
    $bindvars = array($whois_ext);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($whois_id, $whois_ext, $whois_server) = $result->fields;
    if (!xarSecurityCheck('OverviewNetquery')) return;
    $link = array('whois_id'     => $whois_id,
                  'whois_ext'    => $whois_ext,
                  'whois_server' => $whois_server);
    $result->Close();
    return $link;
}
?>