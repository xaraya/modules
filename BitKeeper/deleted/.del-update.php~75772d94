<?php
/**
 * update a whois lookup link
 */
function netquery_adminapi_update($args)
{
    extract($args);
    if ((!isset($whois_id)) || (!isset($whois_ext)) || (!isset($whois_server))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery',
                          'admin',
                          'getlink',
                          array('whois_id' => $whois_id));
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
                 SET whois_ext    = ?,
                      whois_server = ?
               WHERE whois_id = ?";
    $result =& $dbconn->Execute($query, array((string) $whois_ext, (string) $whois_server, (int) $whois_id));
    if (!$result) return;
    return true;
}
?>
