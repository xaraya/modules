<?php
function netquery_admin_lgdelete()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('router_id','int',$router_id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data = xarModAPIFunc('netquery', 'admin', 'getrouter', array('router_id' => $router_id));
    if ($data == false) return;
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['confirminfo'] = xarML('Router Name').": ".$data['router']." - ".xarML('Address').": ".$data['address'];
    $data['submitlabel'] = xarML('Confirm');
    $data['cancellabel'] = xarML('Cancel');
    if (empty($confirmation))
    {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if ((!isset($Submit)) || ($Submit != xarML('Confirm')))
    {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'lgview'));
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery', 'admin', 'lgremove', array('router_id' => $router_id))) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'lgview'));
    return $data;
}
?>