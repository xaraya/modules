<?php
/**
 * Default user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  Function decides if user is logged in
 * and returns user to correct location.
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
*/
function members_user_main()
{
    $baseurl = xarServerGetBaseURL();
    if (xarUserIsLoggedIn()) {
       xarResponseRedirect(xarModURL('members','user', 'view'));
    } else {
        xarResponseRedirect($baseurl);
    }    return true;
}

?>