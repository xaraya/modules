<?php
function netquery_admin_clearlog()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['confirminfo'] = xarML('Clear Netquery Log');
    $data['submitlabel'] = xarML('Confirm');
    $data['cancellabel'] = xarML('Cancel');
    if (empty($confirmation)) {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if ((!isset($Submit)) || ($Submit != xarML('Confirm'))) {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery', 'admin', 'dellog')) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
    return $data;
}
?>