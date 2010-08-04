<?php
/**
 * Authenticate a user against the Xaraya database, using their email address
 * and password.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authemail Module
 * @link http://xaraya.com/index.php/release/10513.html
 * @author Roger Keays <r.keays@ninthave.net>
 */

 /*
 * @public
 * @author Marco Canini modified by Roger Keays for authemail
 * @param args['uname'] email address of user
 * @param args['pass'] password of user
 * @returns int
 * @return uid on successful authentication, XARUSER_AUTH_FAILED otherwise
 */
function authemail_userapi_authenticate_user($args)
{
    extract($args);
    if (!isset($uname) || !isset($pass) || $pass == "") {
        $msg = xarML('Empty uname (#(1)) or pass (not shown).', $uname);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return XARUSER_AUTH_FAILED;
    }

    $email = $uname;
    // Get user information from roles
    $userRole = xarModAPIFunc('roles', 'user', 'get', array('email' => $email));
    if (!is_array($userRole))  return  XARUSER_AUTH_FAILED;
    
    $uid =  $userRole['uid'];
    $realpass = $userRole['pass'];
    $state = $userRole['state'];
    $uname = $userRole['uname'];
    //we return XARUSER_AUTH_FAILED if user is not available or we checked the password and  pass is wrong
    // we return $uid = NULL if state is not active so the correct error messages is sent from original login
    switch($state) {

        case ROLES_STATE_DELETED:
            // User is deleted by all means.  Return a message that says the same.
            $msg = xarML('Your account has been removed at your request, or at the site administrator\'s discretion.');
            xarErrorSet(XAR_USER_EXCEPTION, 'LOGIN_ERROR', new DefaultUserException($msg));
            return null;

        case ROLES_STATE_INACTIVE:
            // User is inactive.  Return message stating.
            $msg = xarML('Your account is marked as inactive.  Contact the site administrator if you have further questions.');
            xarErrorSet(XAR_USER_EXCEPTION, 'LOGIN_ERROR', new DefaultUserException($msg));
            return null;

        case ROLES_STATE_NOTVALIDATED:
            //User still must validate
            xarResponseRedirect(xarModURL('roles', 'user', 'getvalidation'));
            return null;

        case ROLES_STATE_PENDING:
            // User is pending activation
            $msg = xarML('Your account is pending awaiting activated by the site administrator');
            xarErrorSet(XAR_USER_EXCEPTION, 'LOGIN_ERROR', new DefaultUserException($msg));
            return null;

         case ROLES_STATE_ACTIVE:
         default:
            // Confirm that passwords match
            if (!xarUserComparePasswords($pass, $realpass, $uname, 
                substr($realpass, 0, 2))) {
                return XARUSER_AUTH_FAILED;
            }
         break;
    }

    //ok we are fine, active state, return the $uid
    return (int)$uid;
}
?>