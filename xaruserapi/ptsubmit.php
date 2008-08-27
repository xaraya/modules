<?php
function netquery_userapi_ptsubmit($args)
{
    extract($args);
    if ((!isset($port_port)) || (!isset($port_protocol)) || (!isset($port_service)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if(!xarSecurityCheck('ReadNetquery',0)) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $nextId = $dbconn->GenId($PortsTable);
    $query = "INSERT INTO $PortsTable (
              port_id, port, protocol, service, comment, flag)
              VALUES (?,?,?,?,?,?)";
    $bindvars = array((int)$nextId, (int)$port_port, $port_protocol, $port_service, $port_comment, (int)$port_flag);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    $port_id = $dbconn->PO_Insert_ID($PortsTable, 'port_id');
    return $port_id;
}
?>