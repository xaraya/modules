<?php
/**
 * get a specific whois lookup link
 */

function netquery_adminapi_getwhois($args)
{
    extract($args);
    if (!isset($whois_id)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $query = "SELECT whois_id,
                     whois_ext,
                     whois_server
            FROM $WhoisTable
            WHERE whois_id = " . xarVarPrepForStore($whois_id);
    $result =& $dbconn->Execute($query);
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
