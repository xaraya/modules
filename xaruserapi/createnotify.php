<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
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
 * @author Damien Bonvillain
 * @author Gregor J. Rothfuss
 * @param 'username'
 * @param 'realname'
 * @param 'email'
 * @param 'pass'  password
 * @param 'id'  user id
 * @param 'ip'  user ip (optional)
 * @param 'state'  one of Roles_Master::ROLES_RSTATE_NOTVALIDATED, Roles_Master::ROLES_RSTATE_PENDING, Roles_Master::ROLES_RSTATE_ACTIVE
 * @return true if ok
 */
function registration_userapi_createnotify($args)
{
    extract($args);

    if ($state == Roles_Master::ROLES_RSTATE_NOTVALIDATED) {

        // TODO: make sending mail configurable too, depending on the other options ?
        $emailargs = array( 'id'           => array($id => '1'),
                            'mailtype'     => 'confirmation',
                            'ip'           => xarServer::getVar('REMOTE_ADDR'),
                            'pass'         => $password );

        if (!xarMod::apiFunc('roles', 'admin', 'senduseremail', $emailargs)) {
            $msg = xarML('Problem sending confirmation email');
            throw new BadParameterException('email', $msg);
        }
    }

    if ($state == Roles_Master::ROLES_RSTATE_PENDING || $state == Roles_Master::ROLES_RSTATE_ACTIVE) {
        // Send an e-mail to the admin if notification of new user registration is required,
        // Same  email is added to the 'getvalidation' new users in Roles module

        if (xarModVars::get('registration', 'sendnotice')) {
            if ((xarModVars::get('registration', 'notificationmodule') == 'mailer') && xarModIsAvailable('mailer')) {
                $result = xarMod::apiFunc('mailer','user','send',
                                array(
                                    'name'               => xarModVars::get('registration','adminnessage'),
                                    'recipientname'      => xarModVars::get('mail', 'adminname'),
                                    'recipientaddress'   => xarModVars::get('mail', 'adminmail'),
                                )
                            );
            } elseif (xarModVars::get('registration', 'notificationmodule') == 'mail') {
                $terms= '';
                if (xarModVars::get('registration', 'showterms') == 1) {
                    // User has agreed to the terms and conditions.
                    $terms = xarML('This user has agreed to the site terms and conditions.');
                }

                $emailargs = array(
                                'adminname'     => xarModVars::get('mail', 'adminname'),
                                'adminemail'    => xarModVars::get('registration', 'notifyemail'),
                                'values'        => $emailvalues,
                                'terms'         => $terms);

                if (!xarMod::apiFunc('registration', 'user', 'notifyadmin', $emailargs)) {
                   return; // TODO ...something here if the email is not sent..
                }
            }
        }
    }

    if ($state == Roles_Master::ROLES_RSTATE_ACTIVE) {
         // send welcome email to user(option)
         // This template is used in options for user validation, user validation and user pending, and user pending alone
        if (xarModVars::get('registration', 'sendwelcomeemail')) {
            if ((xarModVars::get('registration', 'notificationmodule') == 'mailer') && xarModIsAvailable('mailer')) {
                $result = xarMod::apiFunc('mailer','user','send',
                                array(
                                    'name'               => xarModVars::get('registration','usermessage'),
                                    'recipientname'      => $args['name'],
                                    'recipientaddress'   => $args['email'],
                                    'data'               => $args,
                                )
                            );
            } elseif (xarModVars::get('registration', 'notificationmodule') == 'mail') {
                $emailargs = array(
                                'id'      => array($id => '1'),
                                'mailtype' => 'welcome' );

                if (!xarMod::apiFunc('roles',  'admin', 'senduseremail', $emailargs)) {
                    $msg = xarML('Problem sending welcome email');
                    throw new BadParameterException('email', $msg);
                }
            }
        }
    }

    return true;
}
?>
