<?php
function netquery_userapi_getlgrequests($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) {
        $startnum = 1;
    }
    if ((!isset($numitems)) || (!is_numeric($numitems))) {
        $numitems = -1;
    }
    $lgrequests = array();
    if (!xarSecurityCheck('OverviewNetquery')) {
        return $lgrequests;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRequestTable = $xartable['netquery_lgrequest'];
    $query = "SELECT * FROM $LGRequestTable ORDER BY request_id";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($request_id, $request, $command, $handler, $argc) = $result->fields;
        $lgrequests[] = array('request_id' => $request_id,
                              'request'    => $request,
                              'command'    => $command,
                              'handler'    => $handler,
                              'argc'       => $argc);
    }
    $result->Close();
    return $lgrequests;
}
?>