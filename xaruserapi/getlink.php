<?php
function netquery_userapi_getlink($args)
{
    extract($args);
    if (!isset($whois_tld))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!xarSecurityCheck('OverviewNetquery',0)) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT * FROM $WhoisTable WHERE whois_tld = ?";
    $bindvars = array($whois_tld);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($whois_id, $whois_tld, $whois_server, $whois_prefix, $whois_suffix, $whois_unfound) = $result->fields;

    $link = array('whois_id'      => $whois_id,
                  'whois_tld'     => $whois_tld,
                  'whois_server'  => $whois_server,
                  'whois_prefix'  => $whois_prefix,
                  'whois_suffix'  => $whois_suffix,
                  'whois_unfound' => $whois_unfound);
    $result->Close();
    return $link;
}
?>