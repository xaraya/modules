<?php
function netquery_admin_ptdelete()
{
    if (!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('port_id','int',$port_id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('Submit', 'str:1:100', $Submit, 'Cancel', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data = xarModAPIFunc('netquery', 'admin', 'getport', array('port_id' => $port_id));
    if ($data == false) return;
    $data['confirminfo'] = xarML('Port: '.$data['port'].' - Protocol: '.$data['protocol'].' - Service: '.$data['service']);
    $data['submitlabel'] = xarML('Confirm');
    $data['cancellabel'] = xarML('Cancel');
    if (empty($confirmation)) {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if ((!isset($Submit)) || ($Submit != 'Confirm')) {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview'));
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery', 'admin', 'ptremove', array('port_id' => $port_id))) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'ptview', array('portnum' => $data['port'])));
    return $data;
}
?>