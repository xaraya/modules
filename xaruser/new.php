<?php
function mybookmarks_user_new()
{
    // Security Check
    if(!xarSecurityCheck('Viewmybookmarks')) return;
    if (!xarVarFetch('url','str',$url, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('submitted', 'int', $data['submitted'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarSecurityCheck('Viewmybookmarks')) return; 
    if (!xarUserIsLoggedIn()) return;
    $uid = xarUserGetVar('uid');
    switch(strtolower($phase)) {
        case 'form':
        default:
            // The user API function is called.
            $data['url'] = $url;
            $data['authid']         = xarSecGenAuthKey();
            $data['submitlabel']    = xarML('Submit');
            $item = array();
            $item['module'] = 'mybookmarks';
            $item['itemtype'] = NULL;
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
            if (!xarVarFetch('name', 'str:1:100', $name)) return;
            if (!xarSecConfirmAuthKey()) return;
            // The API function is called.
            if(!xarModAPIFunc('mybookmarks',
                              'user',
                              'create',
                               array('url'      => $url,
                                     'name'     => $name,
                                     'uid'      => $uid))) return;
            xarResponseRedirect(xarModURL('mybookmarks', 'user', 'new', array('submitted' => 1, 'theme' => 'print')));
            break;
    }
    return $data;
}
?>