<?php
/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 * 
 * @param  $phase is the which part of the loop you are on
 */
function pmember_user_usermenu()
{ 
    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if (!xarSecurityCheck('ViewPMember')) return; 
    // First, lets find out where we are in our logic.  If the phase
    // variable is set, we will load the correct page in the loop.
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($phase)) {
        case 'menu': 
            // We need to define the icon that will go into the page.
            //$icon = 'modules/example/xarimages/preferences.gif'; 
            // Now lets send the data to the template which name we choose here.
            $data = xarTplModule('pmember', 'user', 'user_menu_icon');
            break;

        case 'form': 
            $data = xarTplModule('pmember', 'user', 'user_menu_form');
            break;
    } 
    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}
?>