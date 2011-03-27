<?php
/**
 * Display the user menu hook
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @author the Example module development team
 * @param  string $args['phase'] is the which part of the loop you are on
 */
function example_user_usermenu($args)
{
    extract($args);
    /* Security check  - if the user has read access to the menu, show a
     * link to display the details of the item
     */
    /* Don't show an error in user menu if user does not have privs
       Doing so will block the usermenu appearing and any other tabs they do have privs to
       Just don't display this module's User Menu Tab
     */
     if (!xarSecurityCheck('ViewExample',0)) {
         $data='';
         /* Make sure in this specific case return empty (not null) so hooks continue. */
         return $data;
     }
    /* First, lets find out where we are in our logic. If the phase
     * variable is set, we will load the correct page in the loop.
     */
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'menu':
            /* We need to define the icon that will go into the page. */
            $icon = 'modules/example/xarimages/preferences.gif';

            /* Now lets send the data to the template which name we choose here. */
            $data = xarTplModule('example', 'user', 'usermenu_icon', array('iconbasic' => $icon));

            return $data;

        case 'form':
            /* Its good practice for the user menu to be personalized. In order to do so, we
             * need to get some information about the user.
             */
            $name = xarUserGetVar('name');
            $uid = xarUserGetVar('id');
            /* We also need to set the SecAuthKey, in order to stop hackers from setting user
             * vars off site.
             */
            $authid = xarSecGenAuthKey('example');
            /* Lets get the value that we want to override from the preferences. Notice that we are
             * xarModUserGetVar and not xarModVars::get so we can grab the overridden value. You do
             * not have to use a user variable for every module var that the module posses, just
             * the variables that you want to override.
             */
            $value = xarModUserVars::get('example', 'itemsperpage');
            /* Now lets send the data to the template which name we choose here.
             */
            $data = xarTplModule('example', 'user', 'usermenu_form', array('authid' => $authid,
                    'name' => $name,
                    'uid' => $uid,
                    'value' => $value));

            return $data;

        case 'update':
            /* First we need to get the data back from the template in order to process it.
             * The example module is not setting any user vars at this time, but an example
             * might be the number of items to be displayed per page.
             */
            if (!xarVarFetch('itemsperpage', 'int:1:100', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;

            /* Confirm authorisation code. */
            if (!xarSecConfirmAuthKey()) return;

            /* Store the value in an UserVar. Calling a non existent UserVar
             * defaults to a ModuleVar with the same name.
             */
            xarModUserVars::set('example', 'itemsperpage', $itemsperpage);

            /* Redirect back to our form. We could also redirect back to the
             * account page by leaving the array.
             */
            xarResponse::Redirect(xarModURL('roles', 'user', 'account',
                                          array ('moduleload' => 'example')));

            return;
    }
}
?>