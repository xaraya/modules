<?php
function netquery_admin_fldelete()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('flag_id', 'int:1:100000' ,$flag_id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data = xarModAPIFunc('netquery', 'admin', 'getflag', array('flag_id' => $flag_id));
    if ($data == false) return;
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['confirminfo'] = xarML('Keyword').": ".$data['keyword']." - ".xarML('Font').": ".$data['fontclr'];
    $data['submitlabel'] = xarML('Confirm');
    $data['cancellabel'] = xarML('Cancel');
    if (empty($confirmation)) {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if ((!isset($Submit)) || ($Submit != xarML('Confirm'))) {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'flview'));
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery', 'admin', 'flremove', array('flag_id' => $flag_id))) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'flview'));
    return $data;
}
?>