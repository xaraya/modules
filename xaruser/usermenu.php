<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * The user menu that is used in roles/account
 */
function comments_user_usermenu($args)
{
    extract($args);

    // Security Check
    if (xarSecurityCheck('Comments-Read',0)) {

    if(!xarVarFetch('phase','str', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}

        xarTplSetPageTitle(xarModGetVar('themes', 'SiteName').' :: '.
                           xarVarPrepForDisplay(xarML('Comments'))
                           .' :: '.xarVarPrepForDisplay(xarML('Your Account Preferences')));

        switch(strtolower($phase)) {
        case 'menu':

            $icon = xarTplGetImage('comments.gif', 'comments');
            $data = xarTplModule('comments','user', 'usermenu_icon',
                array('icon' => $icon,
                      'usermenu_form_url' => xarModURL('comments', 'user', 'usermenu', array('phase' => 'form'))
                     ));
            break;

        case 'form':

            $settings = xarModAPIFunc('comments','user','getoptions');
            $settings['max_depth'] = _COM_MAX_DEPTH - 1;
            $authid = xarSecGenAuthKey('comments');
            $data = xarTplModule('comments','user', 'usermenu_form', array('authid'   => $authid,
                                                                           'settings' => $settings));
            break;

        case 'update':

            if(!xarVarFetch('settings','array', $settings, array(), XARVAR_NOT_REQUIRED)) {return;}

            if (count($settings) <= 0) {
                $msg = xarML('Settings passed from form are empty!');
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
            }

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey())
                return;

            xarModAPIFunc('comments','user','setoptions',$settings);

            // Redirect
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
        }

    } else {
       $data=''; //make sure hooks in usermenu don't fail because this function returns unset
    }
        return $data;
}

?>