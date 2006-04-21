<?php
/**
* Display the user menu hook
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
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param  $phase is the which part of the loop you are on
 */
function ebulletin_user_usermenu($args)
{
    extract($args);

    if (!xarSecurityCheck('VieweBulletin',0)) return '';

          # use phase to decide what to do
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'menu': // get menu tab

            // get icon (do we need this anymore?)
            $icon = xarTplGetImage('preferences.gif', 'ebulletin');

            // should this tab be active?
            $currenturl = xarServerGetCurrentURL();
            $thispage = xarModURL('roles', 'user', 'account', array('moduleload' => 'ebulletin'));
            $active = ($thispage == $currenturl) ? true : false;

            // get compiled tab
            $data = xarTplModule('ebulletin', 'user', 'usermenu_icon',
                array('iconbasic' => $icon, 'active' => $active, 'url' => $thispage)
            );

            break;

        case 'form':  // show GUI

            // get vars
            $uid = xarUserGetVar('uid');
            $name = xarUserGetVar('name');
            $authid = xarSecGenAuthKey('ebulletin');

            // get user's subscriptions
            $subs = xarModAPIFunc('ebulletin', 'user', 'getsubscriber', array('uid' => $uid));
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

            // take care of status message
            $statusmsg = xarSessionGetVar('statusmsg');
            xarSessionSetVar('statusmsg', '');

            // get compiled template output
            $args = array(
                'name'      => $name,
                'uid'       => $uid,
                'statusmsg' => $statusmsg,
                'pubs'      => $pubs,
                'hidden'    => $hidden,
                'subs'      => $pubs,
                'authid'    => $authid
            );
            $data = xarTplModule('ebulletin', 'user', 'usermenu_form', $args);
            break;

        case 'update': // save options

            // security check
            if (!xarSecConfirmAuthKey()) return;

            // get HTTP vars
            if (!xarVarFetch('uid', 'id', $uid)) return;
            if (!xarVarFetch('subscriptions', 'array', $subscriptions, array(), XARVAR_NOT_REQUIRED)) return;

            // save var
            xarModAPIFunc('ebulletin', 'user', 'updatesubscriptions',
                array('subscriptions' => $subscriptions, 'uid' => $uid)
            );

            // set status message and redirect
            xarSessionSetVar('statusmsg', xarML('Subscription options successfully set!'));
            xarResponseRedirect(xarModURL('roles', 'user', 'account', array('moduleload' => 'ebulletin')));

            break;
    }
    return $data;
}

?>