<?php

/**
 * generate the common admin menu configuration
 */
function logconfig_adminapi_menu()
{
    if (!xarVarFetch('func','str',$activelink, 'main', XARVAR_NOT_REQUIRED)) return;
  
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Logging System Administration');

    $menu['menulinks'] = xarModAPIFunc('logconfig','admin','getmenulinks');

    $menu['activelink'] = $activelink;
    
    // Return the array containing the menu configuration
    return $menu;
}

?>