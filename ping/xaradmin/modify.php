<?php
function ping_admin_modify()
{
    // Get parameters
    if (!xarVarFetch('id','id', $id)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase)) {
        case 'form':
        default:
            // The user API function is called.
            $data = xarModAPIFunc('ping',
                                  'user',
                                  'get',
                                  array('id' => $id));
            if (empty($data)) return;
            // Security Check
            if(!xarSecurityCheck('Adminping')) return;

            $data['module'] = 'ping';
            $data['itemtype'] = 0; // forum
            $data['itemid'] = $id;
            $hooks = xarModCallHooks('item','modify',$id, $data);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            //Load Template
            $data['authid'] = xarSecGenAuthKey();
            $data['submitlabel'] = xarML('Update');
            $data['action'] = '1';
            break;

        case 'update':
            if (!xarVarFetch('url', 'str:1:', $url, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('method','checkbox', $method, false, XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            // The API function is called.
            if(!xarModAPIFunc('ping',
                              'admin',
                              'update',
                               array('id'       => $id,
                                     'url'      => $url,
                                     'method'   => $method))) return;
            // Redirect
            xarResponseRedirect(xarModURL('ping', 'admin', 'view'));
            break;
        }
    return $data;
}
?>