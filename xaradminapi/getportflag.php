<?php
function netquery_adminapi_getportflag($args)
{
    extract($args);
    if (!isset($flag))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $portflag = array();
    if (!xarSecurityCheck('ReadNetquery',0)) return $portflag;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "SELECT * FROM $PortsTable WHERE flag = ?";
    $bindvars = array($flag);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($port_id, $port, $protocol, $service, $comment, $flag) = $result->fields;
        $portflag[] = array('port_id'  => $port_id,
                            'port'     => $port,
                            'protocol' => $protocol,
                            'service'  => $service,
                            'comment'  => $comment,
                            'flag'     => $flag);
    }
    $result->Close();
    return $portflag;
}
?>