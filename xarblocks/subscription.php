<?php
/**
* Subscription block
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
 * initialise block
 *
 * @author Example Module development team
 */
function ebulletin_subscriptionblock_init()
{
    return array();
}

/**
 * get information on block
 */
function ebulletin_subscriptionblock_info()
{
    /* Values */
    return array(
        'text_type' => 'Subscriptions',
        'module' => 'ebulletin',
        'text_type_long' => 'Show GUI for subscribing to newsletters',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function ebulletin_subscriptionblock_display($blockinfo)
{
    // security check
    if (!xarSecurityCheck('ReadeBulletinBlock', 0, 'Block', $blockinfo['title'])) return;

    // get variables
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // if we're currently in the module or in usermenu hook, don't show
    $modname = xarModGetName();
    if ($modname == 'ebulletin') {
        return;
    } elseif ($modname == 'roles') {
        if (!xarVarFetch('moduleload', 'str:1:', $moduleload, '', XARVAR_NOT_REQUIRED)) return;
        if (!empty($moduleload)) return;
    }

    // get vars
    $loggedin = xarUserIsLoggedIn();
    $uid = xarUserGetVar('uid');
    if (empty($name)) $name = xarVarPrepForDisplay(xarUserGetVar('name'));
    $email = ($loggedin) ? xarVarPrepEmailDisplay(xarUserGetVar('email')) : '';
    $authid = xarSecGenAuthKey('ebulletin');

    // get some urls
    $accounturl = xarModURL('roles', 'user', 'account', array('moduleload' => 'roles'));
    $moreurl = xarModURL('ebulletin', 'user', 'main');

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

    // initialize template data
    $data = array();

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
    $data['moreurl']    = $moreurl;

    $blockinfo['content'] = $data;

    return $blockinfo;
}
?>