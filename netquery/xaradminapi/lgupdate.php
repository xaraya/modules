<?php
/**
 * update a looking glass router
 */
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


    $bindvars = array((string)  $router_router,
                      (string)  $router_address,
                      (string)  $router_username,
                      (string)  $router_password,
                      (int)     $router_zebra,
                      (int)     $router_zebra_port,
                      (string)  $router_zebra_password,
                      (int)     $router_ripd,
                      (int)     $router_ripd_port,
                      (string)  $router_ripd_password,
                      (int)     $router_ripngd,
                      (int)     $router_ripngd_port,
                      (string)  $router_ripngd_password,
                      (int)     $router_ospfd,
                      (int)     $router_ospfd_port,
                      (string)  $router_ospfd_password,
                      (int)     $router_bgpd,
                      (int)     $router_bgpd_port,
                      (string)  $router_bgpd_password,
                      (int)     $router_ospf6d,
                      (int)     $router_ospf6d_port,
                      (string)     $router_ospf6d_password,
                      (int)     $router_use_argc,
                      (int)     $router_id);

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    return true;
}
?>
