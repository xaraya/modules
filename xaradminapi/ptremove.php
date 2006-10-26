<?php
function netquery_adminapi_ptremove($args)
{
    extract($args);
    if (!isset($port_id))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery', 'admin', 'getport', array('port_id' => (int)$port_id));
    if (empty($data))
    {
        $msg = xarML('No Such Port Service Present', 'netquery');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('DeleteNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $PortsTable = $xartable['netquery_ports'];
    $query = "DELETE FROM $PortsTable WHERE port_id = ?";
    $result =& $dbconn->Execute($query, array((int)$port_id));
    if (!$result) return;
    return true;
}
?>