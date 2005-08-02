<?php
function netquery_admin_wimodify()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('whois_id', 'int', $whois_id)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase)) {
        case 'form':
        default:
            $data = xarModAPIFunc('netquery', 'admin', 'getlink', array('whois_id' => $whois_id));
            if ($data == false) return;
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $data['cancellabel']    = xarML('Cancel');
            break;
        case 'update':
            if ((!isset($Submit)) || ($Submit != 'Submit')) {
                xarResponseRedirect(xarModURL('netquery', 'admin', 'wiview'));
            }
            if (!xarVarFetch('whois_ext', 'str:1:100', $whois_ext)) return;
            if (!xarVarFetch('whois_server', 'str:1:100', $whois_server, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarSecConfirmAuthKey()) return;
            if (!isset($whois_server) || $whois_server == '') {
                $whois_server = ltrim($whois_ext, " .") . ".whois-servers.net";
                $whois_server = gethostbyname($whois_server);
                $whois_server = gethostbyaddr($whois_server);
            }
            if (!xarModAPIFunc('netquery', 'admin', 'wiupdate',
                               array('whois_id'      => $whois_id,
                                     'whois_ext'     => $whois_ext,
                                     'whois_server'  => $whois_server))) return;
            xarResponseRedirect(xarModURL('netquery', 'admin', 'wiview'));
            break;
    }
    return $data;
}
?>