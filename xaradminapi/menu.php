<?php
/**
   Generate the common admin menu configuration
   TODO: Do something with this func
   
   @return menu options
*/
function mailbag_adminapi_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Mailbag Administration');

    // Return the array containing the menu configuration
    return $menu;
}
?>
