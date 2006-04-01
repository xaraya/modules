<?php
function netquery_admin_ptdelete()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('port_id','int:1:100000',$port_id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pflag', 'int:0:200', $pflag, '-1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data = xarModAPIFunc('netquery', 'admin', 'getport', array('port_id' => $port_id));
    if ($data == false) return;
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['pflag'] = $pflag;
    $data['confirminfo'] = xarML('Port').": ".$data['port']." - ".xarML('Protocol').": ".$data['protocol']." - ".xarML('Service').": ".$data['service'];
    $data['submitlabel'] = xarML('Confirm');
    $data['cancellabel'] = xarML('Cancel');
    if (empty($confirmation)) {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if ((!isset($Submit)) || ($Submit != xarML('Confirm'))) {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $data['port'], 'pflag' => $data['pflag'])));
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery', 'admin', 'ptremove', array('port_id' => $port_id))) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $data['port'], 'pflag' => $data['pflag'])));
    return $data;
}
?>