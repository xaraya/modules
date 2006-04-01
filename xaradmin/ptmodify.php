<?php
function netquery_admin_ptmodify()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('port_id', 'int:1:100000', $port_id)) return;
    if (!xarVarFetch('pflag', 'int:0:200', $pflag, '-1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase)) {
        case 'form':
        default:
            $data = xarModAPIFunc('netquery', 'admin', 'getport', array('port_id' => $port_id));
            if ($data == false) return;
            $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
            $data['flags'] = xarModAPIFunc('netquery', 'user', 'getflags');
            if ($data['flags'] == false) return;
            $data['pflag'] = $pflag;
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $data['cancellabel']    = xarML('Cancel');
            $data['hlplink'] = Array('url'   => xarModURL('netquery', 'admin', 'portlist', array('theme' => 'print', 'portnum' => $data['port'])),
                                     'title' => xarML('List port services data'),
                                     'label' => xarML('Port').' '.$data['port'].' '.xarML('List'));
            break;
        case 'update':
            if (!xarVarFetch('port_port', 'int:1:100000', $port_port)) return;
            if ((!isset($Submit)) || ($Submit != xarML('Submit'))) {
                xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $port_port, 'pflag' => $pflag)));
            }
            if (!xarVarFetch('port_protocol', 'str:1:3', $port_protocol)) return;
            if (!xarVarFetch('port_service', 'str:1:35', $port_service)) return;
            if (!xarVarFetch('port_comment', 'str:1:50', $port_comment, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port_flag', 'int:1:100000', $port_flag, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('netquery', 'admin', 'ptupdate',
                               array('port_id'       => $port_id,
                                     'port_port'     => $port_port,
                                     'port_protocol' => $port_protocol,
                                     'port_service'  => $port_service,
                                     'port_comment'  => $port_comment,
                                     'port_flag'     => $port_flag))) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $port_port, 'pflag' => $pflag)));
            break;
    }
    return $data;
}
?>