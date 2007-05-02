<?php
function ping_admin_delete()
{
    // Get parameters
    if (!xarVarFetch('id','id', $id)) return;
    if (!xarVarFetch('confirmation','id', $confirmation)) return;
    // The user API function is called.
    $data = xarModAPIFunc('ping',
                          'user',
                          'get',
                          array('id' => $id));
    if (empty($data)) return;
    // Security Check
    if(!xarSecurityCheck('Adminping')) return;
    // Check for confirmation.
    if (empty($confirmation)) {
        // for forums that lost their category
        if (!isset($data['id'])) {
            $data['id'] = $id;
        }
        $data['authid'] = xarSecGenAuthKey();
        $data['action'] = '2';
        //Load Template
        return $data;
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('ping',
                       'admin',
                       'delete',
                        array('id' => $id))) return;
    // Redirect
    xarResponseRedirect(xarModURL('ping', 'admin', 'view'));
    // Return
    return true;
}
?>