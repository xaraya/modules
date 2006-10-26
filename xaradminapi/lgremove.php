<?php
function netquery_adminapi_lgremove($args)
{
    extract($args);
    if (!isset($router_id))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery', 'admin', 'getrouter', array('router_id' => (int)$router_id));
    if (empty($data))
    {
        $msg = xarML('No Such Looking Glass Router Present', 'netquery');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('DeleteNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "DELETE FROM $LGRouterTable WHERE router_id = ?";
    $result =& $dbconn->Execute($query, array((int)$router_id));
    if (!$result) return;
    return true;
}
?>