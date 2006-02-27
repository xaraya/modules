<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_userapi_getmenulinks ( $args )
{


    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.


    if (xarSecurityCheck('ViewMessages', 0) == true) {
        // The main menu will look for this array and return it for a tree
        // view of the module. We are just looking for three items in the
        // array, the url, which we need to use the xarModURL function, the
        // title of the link, which will display a tool tip for the module
        // url, in order to keep the label short, and finally the exact label
        // for the function that we are displaying.

        $menulinks[] = array(
            'url'      => xarModURL('messages', 'user', 'display'),
            'title'    => 'Look at the Messages',
            'label'    => 'Display Messages' );

        $menulinks[] = array(
            'url'      => xarModURL('messages', 'user', 'send', array('action' => 'post')),
            'title'    => 'Send a message to someone',
            'label'    => 'Send Message' );

    } else {
        $menulinks = '';
    }

    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;

}

?>
