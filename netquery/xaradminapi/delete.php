<?php
/**
 * delete a whois lookup link
 */
 
function netquery_adminapi_delete($args)
{
    extract($args);
    if (!isset($whois_id)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $whoislink = xarModAPIFunc('netquery',
                          'admin',
                          'get',
                          array('whois_id' => $whois_id));
    if (empty($whoislink)) {
        $msg = xarML('No Such Whois Lookup Link Present', 'netquery');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    if(!xarSecurityCheck('DeleteNetquery')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $query = "DELETE FROM $WhoisTable
            WHERE whois_id = " . xarVarPrepForStore($whois_id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}
?>