<?php
/**
    Displays stats for the Helpdesk
    TODO: This whole function needs to be programmed
    
    @author Brian McGilligan
    @return Template data
*/
function helpdesk_user_showstats()
{
    $data['menu']      = xarModFunc('helpdesk', 'user', 'menu');
        
    $username = xarUserGetVar('uname');
    $userid   = xarUserGetVar('uid');
    
    $data['summary']   = xarModFunc('helpdesk', 'user', 'summaryfooter');
    
    return xarTplModule('helpdesk', 'user', 'showstats', $data);
}
?>
