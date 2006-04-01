<?php
function netquery_adminapi_wicreate($args)
{
    extract($args);
    if ((!isset($whois_tld)) || (!isset($whois_server))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if(!xarSecurityCheck('AddNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $nextId = $dbconn->GenId($WhoisTable);
    $query = "INSERT INTO $WhoisTable (whois_id, whois_tld, whois_server, whois_prefix, whois_suffix, whois_unfound) VALUES (?,?,?,?,?,?)";
    $bindvars = array($nextId, $whois_tld, $whois_server, $whois_prefix, $whois_suffix, $whois_unfound);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    $whois_id = $dbconn->PO_Insert_ID($WhoisTable, 'whois_id');
    return $whois_id;
}
?>