<?php

/**
 * generate the common admin menu configuration
 */
function xarlinkme_adminapi_menu()
{ 
    // Initialise the array that will hold the menu configuration
    $menu = array(); 
    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('xarLinkMe');
    // Specify the menu labels to be used in your blocklayout template
    // Preset some status variable
    $menu['status'] = '';

    return $menu;
} 

?>
