<?php
function netquery_userapi_getlgrouter($args)
{
    extract($args);
    if (!isset($router))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "SELECT * FROM $LGRouterTable WHERE router = ?";
    $bindvars = array($router);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($router_id,
         $router,
         $address,
         $username,
         $password,
         $zebra,
         $zebra_port,
         $zebra_password,
         $ripd,
         $ripd_port,
         $ripd_password,
         $ripngd,
         $ripngd_port,
         $ripngd_password,
         $ospfd,
         $ospfd_port,
         $ospfd_password,
         $bgpd,
         $bgpd_port,
         $bgpd_password,
         $ospf6d,
         $ospf6d_port,
         $ospf6d_password,
         $use_argc) = $result->fields;
    if (!xarSecurityCheck('ReadNetquery',0)) return;
    $lgrouter = array('router_id'       => $router_id,
                      'router'          => $router,
                      'address'         => $address,
                      'username'        => $username,
                      'password'        => $password,
                      'zebra'           => $zebra,
                      'zebra_port'      => $zebra_port,
                      'zebra_password'  => $zebra_password,
                      'ripd'            => $ripd,
                      'ripd_port'       => $ripd_port,
                      'ripd_password'   => $zebra_password,
                      'ripngd'          => $ripngd,
                      'ripngd_port'     => $ripngd_port,
                      'ripngd_password' => $ripngd_password,
                      'ospfd'           => $ospfd,
                      'ospfd_port'      => $ospfd_port,
                      'ospfd_password'  => $ospfd_password,
                      'bgpd'            => $bgpd,
                      'bgpd_port'       => $bgpd_port,
                      'bgpd_password'   => $bgpd_password,
                      'ospf6d'          => $ospf6d,
                      'ospf6d_port'     => $ospf6d_port,
                      'ospf6d_password' => $ospf6d_password,
                      'use_argc'        => $use_argc);
    $result->Close();
    return $lgrouter;
}
?>