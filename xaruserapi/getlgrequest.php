<?php
function netquery_userapi_getlgrequest($args)
{
    extract($args);
    if (!isset($request)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRequestTable = $xartable['netquery_lgrequest'];
    $query = "SELECT * FROM $LGRequestTable WHERE request = ?";
    $bindvars = array($request);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($request_id, $request, $command, $handler, $argc) = $result->fields;
    if (!xarSecurityCheck('OverviewNetquery')) return;
    $lgrequest = array('request_id' => $request_id,
                       'request'    => $request,
                       'command'    => $command,
                       'handler'    => $handler,
                       'argc'       => $argc);
    $result->Close();
    return $lgrequest;
}
?>