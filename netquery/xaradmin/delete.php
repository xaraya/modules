<?php

function netquery_admin_delete()
{
    if(!xarSecurityCheck('DeleteNetquery')) return;
    if (!xarVarFetch('whois_id','int',$whois_id)) return;
    if (!xarVarFetch('confirmation','id',$confirmation, '',XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('netquery',
                          'admin',
                          'get',
                          array('whois_id' => $whois_id));

    if ($data == false) return;

    if (empty($confirmation)) {
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('netquery',
                       'admin',
                       'delete', 
                        array('whois_id' => $whois_id))) return;
    xarResponseRedirect(xarModURL('netquery', 'admin', 'view'));
    return true;
}
?>