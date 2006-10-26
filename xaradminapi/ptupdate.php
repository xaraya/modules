<?php
function netquery_adminapi_ptupdate($args)
{
    extract($args);
    if ((!isset($port_id)) || (!isset($port_port)) || (!isset($port_protocol)) || (!isset($port_service)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery', 'admin', 'getport', array('port_id' => (int)$port_id));
    if ($data == false)
    {
        $msg = xarML('No Such Port Service Present', 'netquery');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('EditNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "UPDATE $PortsTable
        SET port     = ?,
            protocol = ?,
            service  = ?,
            comment  = ?,
            flag  = ?
        WHERE port_id = ?";
    $bindvars = array((int)$port_port, $port_protocol, $port_service, $port_comment, (int)$port_flag, (int)$port_id);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    return true;
}
?>
