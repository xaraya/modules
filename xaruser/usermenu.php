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
 * @param string $phase is the which part of the loop you are on
 */
function newsletter_user_usermenu($args)
{
    extract($args);

    if (!xarSecurityCheck('ReadNewsletter')) return;

          # use phase to decide what to do
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'menu': // get menu tab

            // get icon (do we need this anymore?)
            $icon = xarTplGetImage('preferences.gif', 'newsletter');

            // should this tab be active?
            $currenturl = xarServerGetCurrentURL();
            $thispage = xarModURL('roles', 'user', 'account', array('moduleload' => 'newsletter'));
            $active = ($thispage == $currenturl) ? true : false;

            // get compiled tab
            $data = xarTplModule('newsletter', 'user', 'usermenu_icon',
                array('iconbasic' => $icon, 'active' => $active, 'url' => $thispage)
            );

            break;

        case 'form':  // show GUI

            // get vars
            $uid = xarUserGetVar('uid');
            $name = xarUserGetVar('name');
            $authid = xarSecGenAuthKey('newsletter');

            // get public publications
            $pubs = xarModAPIFunc('newsletter','user','get',array('phase' => 'publication',
                                                                  'sortby' => 'title'));
            if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

             $htmlmail = '0';

            // get user's subscriptions
            $subs=array();
            for ($idx = 0; $idx < count($pubs); $idx++) {
            $subs = xarModAPIFunc('newsletter',
                                           'user',
                                           'get',
                                            array('id' => 0, // doesn't matter
                                                  'uid' => $uid,
                                                  'pid' => $pubs[$idx]['id'],
                                                  'phase' => 'subscription'));

             if (count($subs) == 0) {
                $pubs[$idx]['subscribed'] = false;
            } else {
                $pubs[$idx]['subscribed'] = true;
                // Doesn't matter which subscription we grab - they
                // should all be either html or text mail
                //$pubs[$idx]['htmlmail'] = $subs[0]['htmlmail'];
                $htmlmail = $subs[0]['htmlmail'];
            }

            }

            if (empty($subs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


            // get compiled template output
            $args = array('name' => $name,
                          'uid' => $uid,
                          'pubs' => $pubs,
                          'subs' => $pubs,
                          'htmlmail' => $htmlmail,
                          'authid' => $authid);

           //include('c:\wamp\www\phpDump.class.php');
           //dump($pubs);

           $data = xarTplModule('newsletter', 'user', 'usermenu_form', $args);

            break;

        case 'update': // save options

        if (!xarVarFetch('uid', 'id', $uid)) return;
        if (!xarVarFetch('htmlmail', 'int:0:1:', $htmlmail, 0)) return;
        if (!xarVarFetch('pids', 'array:1:', $pids, array(), XARVAR_NOT_REQUIRED)) return;

           // security check
            if (!xarSecConfirmAuthKey()) return;
    $authid = xarSecGenAuthKey('newsletter');
            // save var
            xarModFunc('newsletter', 'user', 'updateusersubscription',array('pids' => $pids,
                                                                        'uid' => $uid,
                                                                        'htmlmail' => $htmlmail,
                                                                        'authid' =>  $authid));

            // set status message and redirect
            //xarSessionSetVar('statusmsg', xarML('Subscription options successfully set!'));
            xarResponseRedirect(xarModURL('roles', 'user', 'account', array('moduleload' => 'newsletter')));

            break;
    }
    return $data;

}
?>
