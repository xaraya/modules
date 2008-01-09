<?php
function netquery_adminapi_getport($args)
{
    extract($args);
    if (!isset($port_id))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!xarSecurityCheck('ReadNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "SELECT * FROM $PortsTable WHERE port_id = ?";
    $bindvars = array((int)$port_id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($port_id, $port, $protocol, $service, $comment, $flag) = $result->fields;

    $port = array('port_id'  => $port_id,
                  'port'     => $port,
                  'protocol' => $protocol,
                  'service'  => $service,
                  'comment'  => $comment,
                  'flag'     => $flag);
    $result->Close();
    return $port;
}
?>