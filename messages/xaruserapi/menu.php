<?php

/**
 * generate the common menu configuration
 */
function messages_userapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarMLByKey('MESSAGES');

    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarMLByKey('MESSAGESVIEW');
    $menu['menulink_view'] = xarModURL('messages','user','view');

    // Return the array containing the menu configuration
    return $menu;
}

?>
