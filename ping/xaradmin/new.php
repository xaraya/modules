<?php
function ping_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('Adminping')) return;
    // Get parameters
    if (!xarVarFetch('url', 'str:1:', $url, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method','checkbox', $method, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {
        case 'form':
        default:
            $item = array();
            $item['module'] = 'ping';
            $item['itemtype'] = 0; // forum
            $hooks = xarModCallHooks('item', 'new','', $item); // forum
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            $data['url'] = $url;
            $data['method'] = $method;
            $data['submitlabel'] = xarML('Create');
            $data['authid'] = xarSecGenAuthKey();
            break;
        case 'update':
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            // The API function is called
            $newfid= xarModAPIFunc('ping',
                                   'admin',
                                   'create',
                               array('url'    => $url,
                                     'method' => $method));
            if (!$newfid) return; 
            xarResponseRedirect(xarModURL('ping', 'admin', 'view'));
            break;
    }
    // Return the output
 return $data;
}
?>