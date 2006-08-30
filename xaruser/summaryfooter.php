<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    Displays the a summary of ticket stats for a user
    @author Brian McGilligan
    @return Template data
*/
function helpdesk_user_summaryfooter()
{
    // Security check
    //if( !Security::check(SECURITY_READ, 'helpdesk') ){ return false; }

    $data['total_tickets'] = xarModAPIFunc('helpdesk', 'user', 'getstats');

    $data['userisloggedin']  = xarUserIsLoggedIn();

    if( Security::check(SECURITY_READ, 'helpdesk', 0, 0, false) )
    {
        $data['username']    = xarUserGetVar('uname');
        $data['userid']      = xarUserGetVar('uid');
        $data['userstats']   = xarModAPIFunc('helpdesk', 'user', 'getuserticketstats',
             array('userid' => $data['userid'])
        );
        $data['enablemystatshyperlink'] = xarModGetVar('helpdesk', 'EnableMyStatsHyperLink');
    }

    return xarTplModule('helpdesk', 'user', 'summaryfooter', $data);
}
?>
