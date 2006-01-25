<?php
/**
* Subscribe a user
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * Subscribe a user
 */
function ebulletin_user_subscribe($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('invalid', 'array', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subscriptions', 'array', $subscriptions, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email', 'str:1:', $email, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (empty($subscriptions)) $subscriptions = array();
    if (empty($email)) $email = '';
    if (empty($name)) $name = '';

    // important from the start: are we logged in?
    $loggedin = xarUserIsLoggedIn();

    // validate vars
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*'
        . '@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (!$loggedin && !preg_match($email_regexp, $email)) {
        $invalid['email'] = 1;
    }
    if (!$loggedin && empty($name)) {
        $invalid['name'] = 1;
    }

    // assemble inputs for ease of handling
    $inputs = array();
    $inputs['subscriptions'] = $subscriptions;
    $inputs['email'] = $email;
    $inputs['name'] = $name;

    // check if we have any errors
    if (count($invalid) > 0) {
        $inputs['invalid'] = $invalid;
        return xarModFunc('ebulletin', 'user', 'main', $inputs);
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // get UID if logged in
    if ($loggedin) {

        $uid = xarUserGetVar('uid');

    // otherwise check whether we really should continue with this request
    } else {

        // check if we're a registered user of this website
        $roles = new xarRoles();
        // TODO: find a Xar-sanctified way of locating a role without calling
        // a private function (only alternative right now seems to be
        // querying the xar_roles table directly)
        $user = $roles->_lookuprole('xar_email', $email);

        // if we found a user, tell them to log in
        if (!empty($user)) {
            // initialize template vars
            $data = xarModAPIFunc('ebulletin', 'user', 'menu', array('tab' => 'subscriptions'));

            // set template vars
            $data['email'] = $email;

            return $data;
        }

        // store name and email for session use (future: cookies?)'
        if (!$loggedin) {
            xarSessionSetVar('ebulletin_name', $name);
            xarSessionSetVar('ebulletin_email', $email);
        }

        // handle validation if required
        if (xarModGetVar('ebulletin', 'requirevalidation')) {
            return xarModFunc('ebulletin', 'user', 'validatesubscriber', $inputs);
        }
    }

    // let API func do the updating
    if (!xarModAPIFunc('ebulletin', 'user', 'updatesubscriptions', array(
        'subscriptions' => $subscriptions,
        'name' => $name,
        'email' => $loggedin ? $uid : $email,
    ))) return;

    // set status message and redirect
    xarSessionSetVar('statusmsg', xarML('Subscription options successfully saved!'));
    xarResponseRedirect(xarModURL('ebulletin', 'user', 'main'));

}

?>
