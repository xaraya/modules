<?php
/**
 * Displays the dynamic user menu.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 */
/**
 * Displays the dynamic user menu.  Currently does not work, due to design
 * of menu not in place, and DD not in place.
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 * @todo    Finish this function.
 */
function members_user_account()
{
    if(!xarVarFetch('moduleload','str', $data['moduleload'], '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarUserIsLoggedIn()){
        xarResponseRedirect(xarModURL(xarModGetNameFromID(xarModGetVar('roles','defaultauthmodule')),
                                      'user',
                                      'register'));
    }

    $data['uid'] = xarUserGetVar('uid');
    $data['name'] = xarUserGetVar('name');
    if ($data['uid'] == XARUSER_LAST_RESORT) {
        $data['message'] = xarML('You are logged in as the last resort administrator.');
    } else  {
        $data['current'] = xarModURL('members', 'user', 'display', array('itemid' => xarUserGetVar('uid')));

        $output = array();
        $output = xarModCallHooks('item', 'usermenu', '', array('module' => 'members'));

        if (empty($output)){
            $message = xarML('There are no account options configured.');
        }
        $data['output'] = $output;

        if (empty($message)){
            $data['message'] = '';
        }
    }
    return $data;
}

?>