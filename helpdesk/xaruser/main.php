<?php
/**
    The main user function
    @author Brian McGilligan
    @return Template data
*/
function helpdesk_user_main()
{
    // Security check
    if (!xarSecurityCheck('readhelpdesk')) return;
    
    if (!xarModGetVar('helpdesk', 'User can Submit') && !xarSecurityCheck('submithelpdesk')) {
        $data['error'] = xarML('Administration has disabled the user interface');
        return $data;
    }
    
    // Add menu to output
    $data['menu']      = xarModFunc('helpdesk', 'user', 'menu');
    
    $data['summary']   = xarModFunc('helpdesk', 'user', 'summaryfooter');
    
    xarTplAddStyleLink('helpdesk', 'style', $fileExt = 'css');    
    
    return xarTplModule('helpdesk', 'user', 'main', $data);
}
?>
