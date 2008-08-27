<?php
function netquery_adminapi_countportflag($args)
{
    extract($args);
    if (!isset($flag))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if(!xarSecurityCheck('ReadNetquery',0)) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "SELECT COUNT(1) FROM $PortsTable WHERE flag = ?";
    $bindvars = array((int)$flag);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>