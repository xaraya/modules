<?php
/**
 * Display the user menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param $phase is the which part of the loop you are on
 *
 */
function dyn_example_user_usermenu($args)
{
    extract($args);
    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if (!xarSecurityCheck('ViewDynExample')) return;

    // First, lets find out where we are in our logic. If the phase
    // variable is set, we will load the correct page in the loop.
    if(!xarVarFetch('phase','str', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}

    switch(strtolower($phase)) {
        case 'menu':

            // We need to define the icon that will go into the page.
            $icon = 'modules/example/xarimages/admin.gif';

            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('dyn_example','user', 'usermenu_icon', array('iconbasic'    => $icon));

            break;

        case 'form':

            // Its good practice for the user menu to be personalized.  In order to do so, we
            // need to get some information about the user.
            $name = xarUserGetVar('name');

            // We also need to set the SecAuthKey, in order to stop hackers from setting user
            // vars off site.
            $authid = xarSecGenAuthKey('dyn_example');

            $submitlabel = xarML('Submit');
            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('dyn_example','user', 'usermenu_form', array('authid'      => $authid,
                                                                              'submitlabel' => $submitlabel,
                                                                              'name'        => $name));
            break;

        case 'update':
            // First we need to get the data back from the template in order to process it.
            // The example module is not setting any user vars at this time, but an example
            // might be the number of items to be displayed per page.
            if(!xarVarFetch('uid','int', $uid, 0, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('name','str', $name, '', XARVAR_NOT_REQUIRED)) {return;}

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            // Redirect back to the account page.  We could also redirect back to our form page as
            // well by adding the phase variable to the array.
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
    }

    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}

?>
