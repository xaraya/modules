<?php
/**
 * AuthSSO User API
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSSO
 * @link http://xaraya.com/index.php/release/51.html
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * Login the externally authenticated user on Server Request
 * 
 * @author Jonn Beames <jsb@xaraya.com>
 * @returns bool
 */
function authsso_eventapi_OnServerRequest()
{
    if (!xarUserIsLoggedIn() && (xarModGetName() != 'authsystem')) {
        xarModAPIFunc('authsystem', 'user', 'login', array('uname'=>'blah', 'pass'=>'blah'));
    }
    return;
}

?>