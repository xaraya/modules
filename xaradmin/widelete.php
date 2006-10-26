<?php
function netquery_admin_widelete()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('whois_id','int',$whois_id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data = xarModAPIFunc('netquery', 'admin', 'getlink', array('whois_id' => $whois_id));
    if ($data == false) return;
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['confirminfo'] = xarML('TLD').": ".$data['whois_tld']." - ".xarML('Server').": ".$data['whois_server'];
    $data['submitlabel'] = xarML('Confirm');
    $data['cancellabel'] = xarML('Cancel');
    if (empty($confirmation))
    {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if ((!isset($Submit)) || ($Submit != xarML('Confirm')))
    {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'wiview'));
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery', 'admin', 'wiremove', array('whois_id' => $whois_id))) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'wiview'));
    return $data;
}
?>