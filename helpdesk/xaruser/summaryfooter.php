<?php
/**
    Displays the a summary of ticket stats for a user
    @author Brian McGilligan
    @return Template data
*/
function helpdesk_user_summaryfooter()
{
    // Security check
    if (!xarSecurityCheck('readhelpdesk')) return;

    $data['return_val'] = xarModAPIFunc('helpdesk',
                                        'user',
                                        'getstats');
    
    $data['userisloggedin']  = xarUserIsLoggedIn();
    
    if ($data['userisloggedin']) {
        $data['username']    = xarUserGetVar('uname');
        $data['userid']      = xarUserGetVar('uid');
        $data['userstats']   = xarModAPIFunc('helpdesk',
                                             'user',
                                             'getuserticketstats', 
                                             array('userid' => $data['userid']));
        
        $data['editaccess']  = xarSecurityCheck('edithelpdesk', 0);
        $data['enablemystatshyperlink'] = xarModGetVar('helpdesk', 'EnableMyStatsHyperLink');
    }
    
    return xarTplModule('helpdesk', 'user', 'summaryfooter', $data);
}
?>
