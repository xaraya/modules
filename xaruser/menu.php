<?php

/**
 * generate the common menu configuration
 */
function events_user_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('EXAMPLE');

    // Specify the menu items to be used in your blocklayout template
    $menu['menulabel_view'] = xarML('EXAMPLEVIEW');
    $menu['menulink_view'] = xarModURL('events','user','view');

    // Specify the labels/links for more menu items if relevant
    // $menu['menulabel_other'] = xarML('Some other menu item');
    // $menu['menulink_other'] = xarModURL('events','user','other');
    // ...

    // Note : you could also put all menu items in a $menu['menuitems'] array
    //
    // Initialise the array that will hold the different menu items
    // $menu['menuitems'] = array();
    //
    // Define a menu item
    // $item = array();
    // $item['menulabel'] = _EXAMPLEVIEW;
    // $item['menulink'] = xarModURL('events','user','view');
    //
    // Add it to the array of menu items
    // $menu['menuitems'][] = $item;
    //
    // Add more menu items to the array
    // ...
    //
    // Then you can let the blocklayout template create the different
    // menu items *dynamically*, e.g. by using something like :
    //
    // <xar:loop name="menuitems">
    //    <td><a href="&xar-var-menulink;">&xar-var-menulabel;</a></td>
    // </xar:loop>
    //
    // in the templates of your module. Or you could even pass an argument
    // to the user_menu() function to turn links on/off automatically
    // depending on which function is currently called...
    //
    // But most people will prefer to specify all this manually in each
    // blocklayout template anyway :-)

    // Return the array containing the menu configuration
    return $menu;
}

?>