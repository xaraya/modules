<?php
/**
* Main user function
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
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 */
function ebulletin_user_main($args)
{
    // security check
    if (!xarSecurityCheck('VieweBulletin')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email', 'str:1:', $email, $email, XARVAR_NOT_REQUIRED)) return;

    // get vars
    $loggedin = xarUserIsLoggedIn();
    $uid = xarUserGetVar('uid');
    if (empty($name)) $name = xarVarPrepForDisplay(xarUserGetVar('name'));
    $email = ($loggedin) ? xarVarPrepEmailDisplay(xarUserGetVar('email')) : '';
    $authid = xarSecGenAuthKey('ebulletin');

    $accounturl = xarModURL('roles', 'user', 'account', array('moduleload' => 'roles'));

    // get user's subscriptions
    $subs = xarModAPIFunc('ebulletin', 'user', 'getsubscriber');
    if (empty($subs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get public publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('public' => true));
    if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get hidden publications
    $hidden = xarModAPIFunc('ebulletin', 'user', 'getall', array('public' => false));
    if (empty($hidden) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // add subscription flag to pubs
    foreach ($subs as $index => $sub) {
        if (isset($pubs[$sub['pid']])) $pubs[$sub['pid']]['subscribed'] = true;
        if (isset($hidden[$sub['pid']])) $hidden[$sub['pid']]['subscribed'] = true;
    }

    // set page title
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('eBulletin')));

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'user', 'menu');

     // set template vars
    $data['name']       = $name;
    $data['email']      = $email;
    $data['uid']        = $uid;
    $data['pubs']       = $pubs;
    $data['hidden']     = $hidden;
    $data['subs']       = $pubs;
    $data['authid']     = $authid;
    $data['loggedin']   = $loggedin;
    $data['accounturl'] = $accounturl;
    $data['invalid']    = $invalid;

    return $data;

}

?>
