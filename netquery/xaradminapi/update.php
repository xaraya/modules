<?php
/**
 * update a whois lookup link
 */

function netquery_adminapi_update($args)
{
    extract($args);
    if ((!isset($whois_id)) || (!isset($whois_ext)) || (!isset($whois_server))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $link = xarModAPIFunc('netquery',
                          'admin',
                          'get',
                          array('whois_id' => $whois_id));

    if ($link == false) {
        $msg = xarML('No Such Whois Lookup Link Present', 'netquery');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    if(!xarSecurityCheck('EditNetquery')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $query = "UPDATE $WhoisTable
            SET whois_ext    = '" . xarVarPrepForStore($whois_ext) . "',
                whois_server = '" . xarVarPrepForStore($whois_server) . "'
            WHERE whois_id = " . xarVarPrepForStore($whois_id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}
?>