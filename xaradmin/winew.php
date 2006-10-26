<?php
function netquery_admin_winew()
{
    if (!xarSecurityCheck('AddNetquery')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase))
    {
        case 'form':
        default:
            $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $data['cancellabel']    = xarML('Cancel');
            break;
        case 'update':
            if ((!isset($Submit)) || ($Submit != xarML('Submit')))
            {
                xarResponseRedirect(xarModURL('netquery', 'admin', 'wiview'));
            }
            if (!xarVarFetch('whois_tld', 'str:1:100', $whois_tld)) return;
            if (!xarVarFetch('whois_server', 'str:1:100', $whois_server, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whois_prefix', 'str:1:100', $whois_prefix, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whois_suffix', 'str:1:100', $whois_suffix, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('whois_unfound', 'str:1:100', $whois_unfound, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!isset($whois_server) || $whois_server == '')
            {
                $whois_server = ltrim($whois_tld, " .") . ".whois-servers.net";
                $whois_server = gethostbyname($whois_server);
                $whois_server = gethostbyaddr($whois_server);
            }
            if (!xarModAPIFunc('netquery', 'admin', 'wicreate',
                               array('whois_tld'     => $whois_tld,
                                     'whois_server'  => $whois_server,
                                     'whois_prefix'  => $whois_prefix,
                                     'whois_suffix'  => $whois_suffix,
                                     'whois_unfound' => $whois_unfound))) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'wiview'));
            break;
    }
    return $data;
}
?>