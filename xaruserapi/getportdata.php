<?php
function netquery_userapi_getportdata($args)
{
    extract($args);
    if (!isset($port))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $portdata = array();
    if (!xarSecurityCheck('OverviewNetquery',0)) return $portdata;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "SELECT * FROM $PortsTable WHERE flag < 99 AND port = ?";
    $bindvars = array($port);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext())
    {
        list($port_id, $port, $protocol, $service, $comment, $flag) = $result->fields;
        $portdata[] = array('port_id'  => $port_id,
                            'port'     => $port,
                            'protocol' => $protocol,
                            'service'  => $service,
                            'comment'  => $comment,
                            'flag'     => $flag);
    }
    $result->Close();
    return $portdata;
}
?>