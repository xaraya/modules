<?php
function netquery_admin_lgmodify()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('router_id', 'int', $router_id)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase))
    {
        case 'form':
        default:
            $data = xarModAPIFunc('netquery', 'admin', 'getrouter', array('router_id' => $router_id));
            if ($data == false) return;
            $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $data['cancellabel']    = xarML('Cancel');
            break;
        case 'update':
            if ((!isset($Submit)) || ($Submit != xarML('Submit')))
            {
                xarResponseRedirect(xarModURL('netquery', 'admin', 'lgview'));
            }
            if (!xarVarFetch('router_router', 'str:1:100', $router_router)) return;
            if (!xarVarFetch('router_address', 'str:1:100', $router_address)) return;
            if (!xarVarFetch('router_username', 'str:1:20', $router_username, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_password', 'str:1:20', $router_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_zebra', 'checkbox', $router_zebra, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_zebra_port', 'int:1:100000', $router_zebra_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_zebra_password', 'str:1:20', $router_zebra_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ripd', 'checkbox', $router_ripd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ripd_port', 'int:1:100000', $router_ripd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ripd_password', 'str:1:20', $router_ripd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ripngd', 'checkbox', $router_ripngd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ripngd_port', 'int:1:100000', $router_ripngd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ripngd_password', 'str:1:20', $router_ripngd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ospfd', 'checkbox', $router_ospfd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ospfd_port', 'int:1:100000', $router_ospfd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ospfd_password', 'str:1:20', $router_ospfd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_bgpd', 'checkbox', $router_bgpd, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_bgpd_port', 'int:1:100000', $router_bgpd_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_bgpd_password', 'str:1:20', $router_bgpd_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ospf6d', 'checkbox', $router_ospf6d, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ospf6d_port', 'int:1:100000', $router_ospf6d_port, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_ospf6d_password', 'str:1:20', $router_ospf6d_password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('router_use_argc', 'checkbox', $router_use_argc, '0', XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('netquery', 'admin', 'lgupdate',
                               array('router_id'              => $router_id,
                                     'router_router'          => $router_router,
                                     'router_address'         => $router_address,
                                     'router_username'        => $router_username,
                                     'router_password'        => $router_password,
                                     'router_zebra'           => $router_zebra,
                                     'router_zebra_port'      => $router_zebra_port,
                                     'router_zebra_password'  => $router_zebra_password,
                                     'router_ripd'            => $router_ripd,
                                     'router_ripd_port'       => $router_ripd_port,
                                     'router_ripd_password'   => $router_ripd_password,
                                     'router_ripngd'          => $router_ripngd,
                                     'router_ripngd_port'     => $router_ripngd_port,
                                     'router_ripngd_password' => $router_ripngd_password,
                                     'router_ospfd'           => $router_ospfd,
                                     'router_ospfd_port'      => $router_ospfd_port,
                                     'router_ospfd_password'  => $router_ospfd_password,
                                     'router_bgpd'            => $router_bgpd,
                                     'router_bgpd_port'       => $router_bgpd_port,
                                     'router_bgpd_password'   => $router_bgpd_password,
                                     'router_ospf6d'          => $router_ospf6d,
                                     'router_ospf6d_port'     => $router_ospf6d_port,
                                     'router_ospf6d_password' => $router_ospf6d_password,
                                     'router_use_argc'        => $router_use_argc))) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'lgview'));
            break;
    }
    return $data;
}
?>