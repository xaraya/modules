<?php
function netquery_adminapi_lgupdate($args)
{
    extract($args);
    if ((!isset($router_id)) || (!isset($router_router)) || (!isset($router_address))) {
        $msg = xarML('Invalid Parameter Count');
         xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $data = xarModAPIFunc('netquery',
                          'admin',
                          'getrouter',
                          array('router_id' => $router_id));
    if ($data == false) {
        $msg = xarML('No Such Looking Glass Router Present', 'netquery');
         xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if(!xarSecurityCheck('EditNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "UPDATE $LGRouterTable
        SET router          = ?,
            address         = ?,
            username        = ?,
            password        = ?,
            zebra           = ?,
            zebra_port      = ?,
            zebra_password  = ?,
            ripd            = ?,
            ripd_port       = ?,
            ripd_password   = ?,
            ripngd          = ?,
            ripngd_port     = ?,
            ripngd_password = ?,
            ospfd           = ?,
            ospfd_port      = ?,
            ospfd_password  = ?,
            bgpd            = ?,
            bgpd_port       = ?,
            bgpd_password   = ?,
            ospf6d          = ?,
            ospf6d_port     = ?,
            ospf6d_password = ?,
            use_argc        = ?
        WHERE router_id = ?";
    $bindvars=array($router_router, $router_address, $router_username, $router_password, $router_zebra, $router_zebra_port,
                    $router_zebra_password, $router_ripd, $router_ripd_port, $router_ripd_password, $router_ripngd, $router_ripngd_port,
                    $router_ripngd_password, $router_ospfd, $router_ospfd_port, $router_ospfd_password, $router_bgpd,
                    $router_pgpd_port, $router_bgpd_password, $router_ospf6d, $router_ospf6d_port, $router_ospf6d_password,
                    $router_usr_argc, $router_id);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    return true;
}
?>
