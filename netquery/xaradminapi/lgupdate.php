<?php
/**
 * update a looking glass router
 */
function netquery_adminapi_lgupdate($args)
{
    extract($args);
    if ((!isset($router_id)) || (!isset($router_router)) || (!isset($router_address))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery',
                          'admin',
                          'getrouter',
                          array('router_id' => $router_id));
    if ($data == false) {
        $msg = xarML('No Such Looking Glass Router Present', 'netquery');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }
    if(!xarSecurityCheck('EditNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "UPDATE $LGRouterTable
        SET router          = '" . xarVarPrepForStore($router_router) . "',
            address         = '" . xarVarPrepForStore($router_address) . "',
            username        = '" . xarVarPrepForStore($router_username) . "',
            password        = '" . xarVarPrepForStore($router_password) . "',
            zebra           = '" . xarVarPrepForStore($router_zebra) . "',
            zebra_port      = '" . xarVarPrepForStore($router_zebra_port) . "',
            zebra_password  = '" . xarVarPrepForStore($router_zebra_password) . "',
            ripd            = '" . xarVarPrepForStore($router_ripd) . "',
            ripd_port       = '" . xarVarPrepForStore($router_ripd_port) . "',
            ripd_password   = '" . xarVarPrepForStore($router_ripd_password) . "',
            ripngd          = '" . xarVarPrepForStore($router_ripngd) . "',
            ripngd_port     = '" . xarVarPrepForStore($router_ripngd_port) . "',
            ripngd_password = '" . xarVarPrepForStore($router_ripngd_password) . "',
            ospfd           = '" . xarVarPrepForStore($router_ospfd) . "',
            ospfd_port      = '" . xarVarPrepForStore($router_ospfd_port) . "',
            ospfd_password  = '" . xarVarPrepForStore($router_ospfd_password) . "',
            bgpd            = '" . xarVarPrepForStore($router_bgpd) . "',
            bgpd_port       = '" . xarVarPrepForStore($router_bgpd_port) . "',
            bgpd_password   = '" . xarVarPrepForStore($router_bgpd_password) . "',
            ospf6d          = '" . xarVarPrepForStore($router_ospf6d) . "',
            ospf6d_port     = '" . xarVarPrepForStore($router_ospf6d_port) . "',
            ospf6d_password = '" . xarVarPrepForStore($router_ospf6d_password) . "',
            use_argc        = '" . xarVarPrepForStore($router_use_argc) . "'
        WHERE router_id = " . xarVarPrepForStore($router_id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    return true;
}
?>