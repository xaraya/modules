<?php
/**
 * create notify- send out email notifications during user create based on state
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/** 
 * @access public
 * @author Jonathan Linowes
 * @author Damien Bonvillain
 * @author Gregor J. Rothfuss
 * @since 1.23 - 2002/02/01
 * @param 'username'
 * @param 'realname'
 * @param 'email'   
 * @param 'pass'  password
 * @param 'uid'  user id
 * @param 'ip'  user ip (optional)
 * @param 'state'  one of ROLES_STATE_NOTVALIDATED, ROLES_STATE_PENDING, ROLES_STATE_ACTIVE
 * @return true if ok
 */
function registration_userapi_createnotify($args)
{
    extract($args);

    if ($state==ROLES_STATE_NOTVALIDATED) {
        if (empty($ip)) {
            $ip = xarServerGetVar('REMOTE_ADDR');
        }
        
        // TODO: make sending mail configurable too, depending on the other options ?
        $emailargs = array( 'uid'           => array($uid => '1'),
                            'mailtype'      => 'confirmation',
                            'ip'            => $ip,
                            'pass'          => $pass ); 

        if (!xarModAPIFunc('roles', 'admin', 'senduseremail', $emailargs)) {
            $msg = xarML('Problem sending confirmation email');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        }
    }

    if ($state==ROLES_STATE_PENDING || $state==ROLES_STATE_ACTIVE) {
        // Send an e-mail to the admin if notification is required,
        // same updated to the getvalidation users in Roles module - need to review that

        if (xarModGetVar('registration', 'sendnotice')) {
            $terms= '';
            if (xarModGetVar('registration', 'showterms') == 1) {
                // User has agreed to the terms and conditions.
                $terms = xarML('This user has agreed to the site terms and conditions.');
            }

            $emailargs = array( 
                            'adminname'     => xarModGetVar('mail', 'adminname'),
                            'adminemail'    => xarModGetVar('registration', 'notifyemail'),
                            'userrealname'  => $realname,
                            'username'      => $username,
                            'useremail'     => $email,
                            'terms'         => $terms,
                            'uid'           => $uid,
                            'userstatus'    => $state );
        
            if (!xarModAPIFunc('registration', 'user', 'notifyadmin', $emailargs)) {
               return; // TODO ...something here if the email is not sent..
            }
        }
    }
    
    if ($state==ROLES_STATE_ACTIVE) {
         // send welcome email (option)
         // MichelV Should this be moved to registration, or stay in roles?
        if (xarModGetVar('registration', 'sendwelcomeemail')) {
            $emailargs = array( 
                            'uid'           => array($uid => '1'),
                            'mailtype'      => 'welcome' );

            if (!xarModAPIFunc('roles',  'admin', 'senduseremail', $emailargs)) {
                $msg = xarML('Problem sending welcome email');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            }
        }
    }

    return true;
}
?>