<?php
function smilies_admin_modify()
{
    // Security Check
    if(!xarSecurityCheck('EditSmilies')) return;
    if (!xarVarFetch('sid','int',$sid)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    switch(strtolower($phase)) {

        case 'form':
        default:
            // The user API function is called.
            $data = xarModAPIFunc('smilies',
                                  'user',
                                  'get',
                                  array('sid' => $sid));
            if ($data == false) return;
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $item = array();
            $item['module'] = 'smilies';
            $item['itemtype'] = NULL; // forum
            $hooks = xarModCallHooks('item','modify','',$item);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            break;
        case 'update':
            // Get parameters
            if (!xarVarFetch('code', 'str:1:100', $code)) return;
            if (!xarVarFetch('icon', 'str:1:100', $icon)) return;
            if (!xarVarFetch('emotion', 'str:1:100', $emotion)) return;
            if (!xarSecConfirmAuthKey()) return;
            // The API function is called.
            if(!xarModAPIFunc('smilies',
                              'admin',
                              'update',
                               array('sid'      => $sid,
                                     'code'     => $code,
                                     'icon'     => $icon,
                                     'emotion'  => $emotion))) return;
            xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));
            break;
    }
	return $data;
}
?>