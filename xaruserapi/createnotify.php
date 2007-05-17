<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage registration
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * Create notification for new users
 *
 * Send out email notifications during user create based on state
 *
 * @access public
 * @author Jonathan Linowes
 * @author jojodee
 * @param object 'object'
 * @param string 'pass'  password
 * @param string 'ip'  user ip (optional)
 * @return bool
 */
function registration_userapi_createnotify($args)
{
    extract($args);

    $uid = $object->properties['id']->value;
    $state = $object->properties['state']->value;

    if ($state == ROLES_STATE_NOTVALIDATED) {
        if (empty($ip)) {
            $ip = xarServerGetVar('REMOTE_ADDR');
        }

        // TODO: make sending mail configurable too, depending on the other options ?
        $emailargs = array( 'uid'           => array($uid => '1'),
                            'mailtype'      => 'confirmation',
                            'ip'            => $ip,
                            'pass'          => $pass);

        if (!xarModAPIFunc('roles', 'admin', 'senduseremail', $emailargs)) {
            $msg = xarML('Problem sending confirmation email');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        }
    }

    if ($state == ROLES_STATE_PENDING || $state == ROLES_STATE_ACTIVE) {
        // Send an e-mail to the admin if notification of new user registration is required,
        // Same  email is added to the 'getvalidation' new users in Roles module

        if (xarModVars::get('registration', 'sendnotice')) {
            $terms= '';
            if (xarModVars::get('registration', 'showterms') == 1) {
                // User has agreed to the terms and conditions.
                $terms = xarML('This user has agreed to the site terms and conditions.');
            }

            $emailargs = $args;
            $emailargs['adminname']  = xarModVars::get('mail', 'adminname');
            $emailargs['adminemail'] = xarModVars::get('registration', 'notifyemail');
            $emailargs['terms']      = $terms;

            if (!xarModAPIFunc('registration', 'user', 'notifyadmin', $emailargs)) {
               return; // TODO ...something here if the email is not sent..
            }
        }
    }

    if ($state == ROLES_STATE_ACTIVE) {
         // send welcome email to user(option)
         // This template is used in options for user validation, user validation and user pending, and user pending alone
        if (xarModVars::get('registration', 'sendwelcomeemail')) {
            $emailargs = array(
                            'uid'      => array($uid => '1'),
                            'mailtype' => 'welcome' );

            if (!xarModAPIFunc('roles',  'admin', 'senduseremail', $emailargs)) {
                $msg = xarML('Problem sending welcome email');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            }
        }
    }

    return true;
}
?>