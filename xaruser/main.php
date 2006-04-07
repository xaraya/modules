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
    if ($loggedin) {
        $uid = xarUserGetVar('uid');
        $name = xarUserGetVar('name');
        $email = xarUserGetVar('email');
    } else {
        // try to retrieve from session
        $uid = '';
        $name = xarSessionGetVar('ebulletin_name');
        $email = xarSessionGetVar('ebulletin_email');
    }

    $authid = xarSecGenAuthKey('ebulletin');
    $accounturl = xarModURL('roles', 'user', 'account', array('moduleload' => 'roles'));

    // get user's subscriptions
    $subs = xarModAPIFunc('ebulletin', 'user', 'getsubscriber',
        array('uid' => $uid, 'name' => $name, 'email' => $email)
    );
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get public publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('public' => true));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get hidden publications
    $hidden = xarModAPIFunc('ebulletin', 'user', 'getall', array('public' => false));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // add subscription flag to pubs
    foreach ($subs as $index => $sub) {
        if (isset($pubs[$sub['pid']])) $pubs[$sub['pid']]['subscribed'] = true;
        if (isset($hidden[$sub['pid']])) $hidden[$sub['pid']]['subscribed'] = true;
    }

    // set page title
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('eBulletin')));

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'user', 'menu', array('tab' => 'subscriptions'));

     // set template vars
    $data['ebulletin_name']  = $name;
    $data['ebulletin_email'] = $email;
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
