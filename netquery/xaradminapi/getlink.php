<?php
/**
 * get a specific whois lookup link
 */
function netquery_adminapi_getlink($args)
{
    extract($args);
    if (!isset($whois_id)) {
        $msg = xarML('Invalid Parameter Count');
         xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "SELECT * FROM $WhoisTable WHERE whois_id = ?";
    $bindvars=array($whois_id);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    list($whois_id, $whois_ext, $whois_server) = $result->fields;
    if(!xarSecurityCheck('OverviewNetquery')) return;
    $data = array('whois_id'     => $whois_id,
                  'whois_ext'    => $whois_ext,
                  'whois_server' => $whois_server);
    $result->Close();
    return $data;
}
?>
