<?php
function netquery_adminapi_lgcreate($args)
{
    extract($args);
    if ((!isset($router_router)) || (!isset($router_address)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if(!xarSecurityCheck('AddNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $nextId = $dbconn->GenId($LGRouterTable);
    $query = "INSERT INTO $LGRouterTable (
              router_id, router, address, username, password,
              zebra, zebra_port, zebra_password,
              ripd, ripd_port, ripd_password,
              ripngd, ripngd_port, ripngd_password,
              ospfd, ospfd_port, ospfd_password,
              bgpd, bgpd_port, bgpd_password,
              ospf6d, ospf6d_port, ospf6d_password,
              use_argc)
              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bindvars = array($nextId, $router_router, $router_address, $router_username, $router_password,
                      (int)$router_zebra, (int)$router_zebra_port, $router_zebra_password,
                      (int)$router_ripd, (int)$router_ripd_port,$router_ripd_password,
                      (int)$router_ripngd, (int)$router_ripngd_port, $router_ripngd_password,
                      (int)$router_ospfd, (int)$router_ospfd_port, $router_ospfd_password,
                      (int)$router_bgpd, (int)$router_bgpd_port, $router_bgpd_password,
                      (int)$router_ospf6d, (int)$router_ospf6d_port, $router_ospf6d_password,
                      (int)$router_use_argc);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    $router_id = $dbconn->PO_Insert_ID($LGRouterTable, 'router_id');
    return $router_id;
}
?>