<?php
/**
 * File: $Id: xaradmin.php,v 1.2 2003/07/02 08:07:14 garrett Exp $
 *
 * AddressBook admin functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

//FIXME: until we figure out module globals
include_once ('modules/addressbook/xarglobal.php');

//=========================================================================
//  the main administration function
//=========================================================================
function AddressBook_admin_main() {

    /**
     * Check if we want to display our overview panel.
     */
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // If you want to go directly to some default function, instead of
        // having a separate main function, you can simply call it here, and
        // use the same template for admin-main.xard as for admin-view.xard
        // return example_admin_view();

        // Initialise the $data variable that will hold the data to be used in
        // the blocklayout template, and get the common menu configuration - it
        // helps if all of the module pages have a standard menu at the top to
        // support easy navigation
        $data = AddressBook_admin_menu();

        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the administration part of this Example module...');

        // Return the template variables defined in this function
        return $data;

        // Note : instead of using the $data variable, you could also specify
        // the different template variables directly in your return statement :
        //
        // return array('menutitle' => ...,
        //              'welcome' => ...,
        //              ... => ...);
    }

} // END main

function AddressBook_admin_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Address Book Administration');

    // Specify the menu labels to be used in your blocklayout template

    // Preset some status variable
    $menu['status'] = '';

    $menu['menuitems'][] = array ('label','#');

    // Note : you could also specify the menu links here, and pass them
    // on to the template as variables
    // $menu['menulink_view'] = xarModURL('example','admin','view');

    // Note : you could also put all menu items in a $menu['menuitems'] array
    //
    // Initialise the array that will hold the different menu items
    // $menu['menuitems'] = array();
    //
    // Define a menu item
    // $item = array();
    // $item['menulabel'] = _EXAMPLEVIEW;
    // $item['menulink'] = xarModURL('example','user','view');
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
    // to the admin_menu() function to turn links on/off automatically
    // depending on which function is currently called...
    //
    // But most people will prefer to specify all this manually in each
    // blocklayout template anyway :-)

    // Return the array containing the menu configuration
    return $menu;
}

?>