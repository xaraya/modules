<?php

/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param $phase is the which part of the loop you are on
 *
 */
function events_user_usermenu()
{

    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if(!xarSecurityCheck('OverviewEvents')) return;

    // First, lets find out where we are in our logic.  If the phase
    // variable is set, we will load the correct page in the loop.
    $phase = xarVarCleanFromInput('phase');

    // Lets set the phase variable in case we are on the icon page.
    // E_ALL fix so we don't have undefined variable.
    if (empty($phase)){
        $phase = 'menu';
    }

    switch(strtolower($phase)) {
        case 'menu':

            // We need to define the icon that will go into the page.
            $icon = 'modules/events/xarimages/preferences.gif';

            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('events','roles', 'usermenu_icon', array('iconbasic'    => $icon));

            break;

        case 'form':

            // Its good practice for the user menu to be personalized.  In order to do so, we
            // need to get some information about the user.
            $name = xarUserGetVar('name');
            $uid = xarUserGetVar('uid');

            // We also need to set the SecAuthKey, in order to stop hackers from setting user
            // vars off site.
            $authid = xarSecGenAuthKey();

            // Lets get the value that we want to override from the preferences. Notice that we are
            // xarModUserGetMod and not xarModGetVar so we can grab the overridden value.  You do
            // not have to use a user variable for every module var that the module posses, just
            // the variables that you want to override.
            $value = xarModGetUserVar('events', 'itemsperpage', $uid);

            //if (empty($value)){
            //    $value = xarModGetVar('events', 'itemsperpage');
            //}

            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('events','user', 'usermenu_form', array('authid'   => $authid,
                                                                          'name'     => $name,
                                                                          'uid'      => $uid,
                                                                          'value'    => $value));
            break;

        case 'update':
            // First we need to get the data back from the template in order to process it.
            // The events module is not setting any user vars at this time, but an events
            // might be the number of items to be displayed per page.
            list($uid,
                 $itemsperpage,
                 $name) = xarVarCleanFromInput('uid',
                                               'itemsperpage',
                                               'name');

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            xarModSetUserVar('events', 'itemsperpage', $itemsperpage, $uid);

            // Redirect back to the account page.  We could also redirect back to our form page as
            // well by adding the phase variable to the array.
            xarResponseRedirect(xarModURL('users', 'user', 'account'));

            break;
    }

    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}


?>