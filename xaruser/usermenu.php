<?php
/**
 * Display the user menu hook
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Display the user menu hook
 * This function will show the personal ITSP for this user
 *
 * @author the ITSP module development team
 * @param  $phase is the which part of the loop you are on
 */
function itsp_user_usermenu($args)
{
    extract($args);
    /* Security check */
     if (!xarSecurityCheck('ViewITSP',0)) {
         return '';
     }

    /* First, lets find out where we are in our logic.  If the phase
     * variable is set, we will load the correct page in the loop.
     */
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'menu':
            /* We need to define the icon that will go into the page. */
            $icon = 'modules/itsp/xarimages/preferences.gif';

            /* Now lets send the data to the template which name we choose here. */
            $data = xarTplModule('itsp', 'user', 'usermenu_icon', array('iconbasic' => $icon));

            break;

        case 'form':
            /* Its good practice for the user menu to be personalized.  In order to do so, we
             * need to get some information about the user.
             */
            $name = xarUserGetVar('name');
            $uid = xarUserGetVar('uid');
            /* We also need to set the SecAuthKey, in order to stop hackers from setting user
             * vars off site.
             */
            $authid = xarSecGenAuthKey('itsp');
            /* Lets get the value that we want to override from the preferences. Notice that we are
             * xarModUserGetVar and not xarModGetVar so we can grab the overridden value.  You do
             * not have to use a user variable for every module var that the module posses, just
             * the variables that you want to override.
             */
            $value = xarModGetUserVar('itsp', 'itemsperpage', $uid);
            /* if (empty($value)){
             * $value = xarModGetVar('itsp', 'itemsperpage');
             * }
             * Now lets send the data to the template which name we choose here.
             */
            $data = xarTplModule('itsp', 'user', 'usermenu_form', array('authid' => $authid,
                    'name' => $name,
                    'uid' => $uid,
                    'value' => $value));
            break;

        case 'update':
            /* First we need to get the data back from the template in order to process it.
             * The itsp module is not setting any user vars at this time, but an itsp
             * might be the number of items to be displayed per page.
             */
            if (!xarVarFetch('uid', 'int:1:', $uid)) return;
            if (!xarVarFetch('itemsperpage', 'str:1:100', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('name', 'str:1:100', $name, '', XARVAR_NOT_REQUIRED)) return;

            /* Confirm authorisation code. */
            if (!xarSecConfirmAuthKey()) return;

            xarModSetUserVar('itsp', 'itemsperpage', $itemsperpage, $uid);
            /* Redirect back to the account page.  We could also redirect back to our form page as
             * well by adding the phase variable to the array.
             */
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
    }
    /* Finally, we need to send our variables to block layout for processing.  Since we are
     * using the data var for processing above, we need to do the same with the return.
     */
    return $data;
}
?>