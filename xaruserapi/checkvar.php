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
 * validate a user variable
 * @access public
 * @author Jonathan Linowes
 * @author Damien Bonvillain
 * @author Gregor J. Rothfuss
 * @since 1.23 - 2002/02/01
 * @param var - the variable to validate
 * @param type - the type of the validation to perform
 * possible type, value:
    'ip' (no var required),
    'email', email str
    'username', username str
    'agreetoterms', not empty (checkbox)
    'pass1', password str
    'pass2', password str
 * @return empty string if the validation was successful, or invalid message otherwise
 */
function registration_userapi_checkvar($args)
{
    extract($args);

    if (empty($type)) {
        $type = 'email';
    }

    $invalid = "";
    switch ($type) {
        case 'ip':
            // TODO: check behind proxies too ?
            // check if the IP address is banned, and if so, throw an exception :)
            if (!isset($var))
                $ip = xarServerGetVar('REMOTE_ADDR');
            else
                $ip = $var;
            $disallowedips = xarModVars::get('registration','disallowedips');
            if (!empty($disallowedips)) {
                $disallowedips = unserialize($disallowedips);
                $disallowedips = explode("\r\n", $disallowedips);
                if (in_array ($ip, $disallowedips)) {
                    $invalid = xarML('Your IP is on the banned list');
                }
            }
            break;

        case 'username':
            $username = $var;
            // check if the username is empty
            if (empty($username)) {
                $invalid = xarML('You must provide a preferred username to continue.');

            // check the length of the username
            } elseif (strlen($username) > 255) {
                $invalid = xarML('Your username is too long.');
            } else {
                // check for duplicate usernames
                $user = xarModAPIFunc('roles', 'user', 'get',
                                array('uname' => $username));

                if ($user != false) {
                    unset($user);
                    $invalid = xarML('That username is already taken.');

                } else {
                    // check for disallowed usernames
                    $disallowednames = xarModVars::get('registration','disallowednames');
                    if (!empty($disallowednames)) {
                        $disallowednames = unserialize($disallowednames);
                        $disallowednames = explode("\r\n", $disallowednames);
                        if (in_array ($username, $disallowednames)) {
                            $invalid = xarML('That username is either reserved or not allowed on this website');
                        }
                    }
                }
            }
            break;

        case 'agreetoterms':
            // kind of dumb, but for completeness
            if (empty($var)){
                $invalid = xarML('You must agree to the terms and conditions of this website to register an account.');
            }
            break;

        case 'pass1':
            break;

        case 'pass2':
            $pass1 = $var[0];
            $pass2 = $var[1];
            if ((empty($pass1)) || (empty($pass2))) {
                $invalid = xarML('You must enter the same password twice');
            } elseif ($pass1 != $pass2) {
                $invalid = xarML('The passwords do not match');
            }
            break;

        case 'email':
        default:
            $email = $var;
            if (empty($email)){
                $invalid = xarML('You must provide a valid email address to continue.');
            } else {
                $invalid = '';

                if (xarModVars::get('registration','uniqueemail')) {
                    // check for duplicate email address
                    $user = xarModAPIFunc('roles', 'user', 'get',
                               array('email' => $email));
                    if ($user != false) {
                        unset($user);
                        $invalid = xarML('That email address is already registered.');
                    }
                }

                if (empty($invalid)) {
                    // check for disallowed email addresses
                    $disallowedemails = xarModVars::get('roles','disallowedemails');
                    if (!empty($disallowedemails)) {
                        $disallowedemails = unserialize($disallowedemails);
                        $disallowedemails = explode("\r\n", $disallowedemails);
                        if (in_array ($email, $disallowedemails)) {
                            $invalid = xarML('That email address is either reserved or not allowed on this website');
                        }
                    }
                }
            }
            break;
    }

    return $invalid;
}
?>