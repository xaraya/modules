<?php

function netquery_admin_modify()
{
    if(!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('whois_id','int',$whois_id)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    switch(strtolower($phase)) {
        case 'form':
        default:
            $data = xarModAPIFunc('netquery',
                                  'admin',
                                  'get',
                                  array('whois_id' => $whois_id));
            if ($data == false) return;
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            break;
        case 'update':
            if (!xarVarFetch('whois_ext', 'str:1:100', $whois_ext)) return;
            if (!xarVarFetch('whois_server', 'str:1:100', $whois_server)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!xarModAPIFunc('netquery',
                              'admin',
                              'update',
                               array('whois_id'      => $whois_id,
                                     'whois_ext'     => $whois_ext,
                                     'whois_server'  => $whois_server))) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'view'));
            break;
    }
    return $data;
}
?>