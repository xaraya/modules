<?php
/**
 * create a new looking glass router
 */
function netquery_adminapi_lgcreate($args)
{
    extract($args);
    if ((!isset($router_router)) ||
        (!isset($router_address))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if(!xarSecurityCheck('AddNetquery')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LGRouterTable = $xartable['netquery_lgrouter'];
    $nextId = $dbconn->GenId($LGRouterTable);
    $query = "INSERT INTO $LGRouterTable (
              router_id,
              router,
              address,
              username,
              password,
              zebra,
              zebra_port,
              zebra_password,
              ripd,
              ripd_port,
              ripd_password,
              ripngd,
              ripngd_port,
              ripngd_password,
              ospfd,
              ospfd_port,
              ospfd_password,
              bgpd,
              bgpd_port,
              bgpd_password,
              ospf6d,
              ospf6d_port,
              ospf6d_password,
              use_argc)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($router_router) . "',
              '" . xarVarPrepForStore($router_address) . "',
              '" . xarVarPrepForStore($router_username) . "',
              '" . xarVarPrepForStore($router_password) . "',
              '" . xarVarPrepForStore($router_zebra) . "',
              '" . xarVarPrepForStore($router_zebra_port) . "',
              '" . xarVarPrepForStore($router_zebra_password) . "',
              '" . xarVarPrepForStore($router_ripd) . "',
              '" . xarVarPrepForStore($router_ripd_port) . "',
              '" . xarVarPrepForStore($router_ripd_password) . "',
              '" . xarVarPrepForStore($router_ripngd) . "',
              '" . xarVarPrepForStore($router_ripngd_port) . "',
              '" . xarVarPrepForStore($router_ripngd_password) . "',
              '" . xarVarPrepForStore($router_ospfd) . "',
              '" . xarVarPrepForStore($router_ospfd_port) . "',
              '" . xarVarPrepForStore($router_ospfd_password) . "',
              '" . xarVarPrepForStore($router_bgpd) . "',
              '" . xarVarPrepForStore($router_bgpd_port) . "',
              '" . xarVarPrepForStore($router_bgpd_password) . "',
              '" . xarVarPrepForStore($router_ospf6d) . "',
              '" . xarVarPrepForStore($router_ospf6d_port) . "',
              '" . xarVarPrepForStore($router_ospf6d_password) . "',
              '" . xarVarPrepForStore($router_use_argc) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $router_id = $dbconn->PO_Insert_ID($LGRouterTable, 'router_id');
    return $router_id;
}
?>