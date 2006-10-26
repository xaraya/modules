<?php
function netquery_userapi_getlgrouters($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) $startnum = 1;
    if ((!isset($numitems)) || (!is_numeric($numitems))) $numitems = 100000;
    $lgrouters = array();
    if (!xarSecurityCheck('OverviewNetquery')) return $lgrouters;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $query = "SELECT * FROM $LGRouterTable ORDER BY router_id";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext())
    {
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
        $lgrouters[] = array('router_id'       => $router_id,
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
    }
    $result->Close();
    return $lgrouters;
}
?>