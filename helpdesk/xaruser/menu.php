<?php
/**
    generate menu fragments
    @param $args['page'] - func calling menu (ex. main, view, etc)
    @return Menu template data
 */
function helpdesk_user_menu()
{            
    if (!xarSecurityCheck('readhelpdesk')) return;
        
    $data['userisloggedin'] = xarUserIsLoggedIn();
            
    xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED);
    xarVarFetch('selection', 'str', $data['selection'],  '', XARVAR_NOT_REQUIRED);
            
    $data['menulinks'] = xarModAPIFunc('helpdesk', 'user', 'menulinks');    
    
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');

    xarTplAddStyleLink('helpdesk', 'style', $fileExt = 'css');    
    
    return xarTplModule('helpdesk', 'user', 'menu', $data);
}
?>
