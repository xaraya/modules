<?php
/**
 * create a new whois lookup link
 */

function netquery_adminapi_create($args)
{
    extract($args);
    if ((!isset($whois_ext)) ||
        (!isset($whois_server))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if(!xarSecurityCheck('AddNetquery')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $WhoisTable = $xartable['netquery_whois'];

    $nextId = $dbconn->GenId($WhoisTable);

    $query = "INSERT INTO $WhoisTable (
              whois_id,
              whois_ext,
              whois_server)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($whois_ext) . "',
              '" . xarVarPrepForStore($whois_server) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $whois_id = $dbconn->PO_Insert_ID($WhoisTable, 'whois_id');

    return $whois_id;
}
?>