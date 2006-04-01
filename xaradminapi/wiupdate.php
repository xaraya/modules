<?php
function netquery_adminapi_wiupdate($args)
{
    extract($args);
    if ((!isset($whois_id)) || (!isset($whois_tld)) || (!isset($whois_server))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery', 'admin', 'getlink', array('whois_id' => (int)$whois_id));
    if ($data == false) {
        $msg = xarML('No Such Whois Lookup Link Present', 'netquery');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('EditNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];
    $query = "UPDATE $WhoisTable
        SET whois_tld    = ?,
            whois_server = ?,
            whois_prefix = ?,
            whois_suffix = ?,
            whois_unfound = ?
        WHERE whois_id = ?";
    $bindvars = array($whois_tld, $whois_server, $whois_prefix, $whois_suffix, $whois_unfound, (int)$whois_id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    return true;
}
?>