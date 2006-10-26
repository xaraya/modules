<?php
function netquery_admin_ptnew()
{
    if (!xarSecurityCheck('AddNetquery')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('portnum', 'int:1:100000', $portnum, '80', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase))
    {
        case 'form':
        default:
            $data['flags'] = xarModAPIFunc('netquery', 'user', 'getflags');
            if ($data['flags'] == false) return;
            $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
            $data['portnum']        = $portnum;
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $data['cancellabel']    = xarML('Cancel');
            break;
        case 'update':
            if ((!isset($Submit)) || ($Submit != xarML('Submit')))
            {
                xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $portnum)));
            }
            if (!xarVarFetch('port_port', 'int:1:100000', $port_port)) return;
            if (!xarVarFetch('port_protocol', 'str:1:3', $port_protocol)) return;
            if (!xarVarFetch('port_service', 'str:1:35', $port_service)) return;
            if (!xarVarFetch('port_comment', 'str:1:50', $port_comment, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port_flag', 'int:1:100000', $port_flag, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('netquery', 'admin', 'ptcreate',
                               array('port_port'     => $port_port,
                                     'port_protocol' => $port_protocol,
                                     'port_service'  => $port_service,
                                     'port_comment'  => $port_comment,
                                     'port_flag'     => $port_flag))) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $port_port)));
            break;
    }
    return $data;
}
?>