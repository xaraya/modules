<?php
function smilies_admin_new()
{   
	if(!xarSecurityCheck('AddSmilies')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase)) {
        case 'form':
        default:
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $item = array();
            $item['module'] = 'smilies';
            $item['itemtype'] = NULL; // forum
            $hooks = xarModCallHooks('item','new','',$item);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            break;
        case 'update':
            if (!xarVarFetch('code', 'str:1:100', $code)) return;
            if (!xarVarFetch('icon', 'str:1:100', $icon)) return;
            if (!xarVarFetch('emotion', 'str:1:100', $emotion)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            // The API function is called
            if (!xarModAPIFunc('smilies',
                               'admin',
                               'create',
                               array('code' => $code,
                                     'icon' => $icon,
                                     'emotion' => $emotion))) return;
            xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));
            break;
    }
    // Return the output
    return $data;
}
?>