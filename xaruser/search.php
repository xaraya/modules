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
    Search Tickets

    Search form for searching the Tickets

    @author  Brian McGilligan bmcgilligan@abrasiontechnology.com
    @access  public / private / protected
    @param
    @param
    @return  template
    @throws  list of exception identifiers which can be thrown
    @todo    <Brian McGilligan> ;
*/
function helpdesk_user_search()
{
    $data['UserLoggedIn'] = xarUserIsLoggedIn();
    $data['menu']         = xarModFunc('helpdesk', 'user', 'menu');

    // Don't allow anonymous users to Search ...
    // Need to change this in the future ?
    $AllowUserCheckStatus  = xarModGetVar('helpdesk', 'User can check status');
    $AllowUserSubmitTicket = xarModGetVar('helpdesk', 'User can Submit');
    $AllowAnonSubmitTicket = xarModGetVar('helpdesk', 'Anonymous can Submit');
    $data['EditAccess']    = xarSecurityCheck('edithelpdesk', 0);
    $AdminAccess = xarSecurityCheck('adminhelpdesk', 0);

    // Security check
    // No need for a security check if Anonymous Adding is enabled:
    // So ONLY check security if AllowAnonAdd is NOT TRUE
    if (!$AllowAnonSubmitTicket){
        if (!xarSecurityCheck('readhelpdesk')) {
            return;
        }
    }

    $data['enabledimages']   = xarModGetVar('helpdesk', 'Enable Images');

    $data['username'] = xarUserGetVar('uname');
    $data['userid'] = xarUserGetVar('uid');

    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');

    return xarTplModule('helpdesk', 'user', 'search', $data);
}
?>