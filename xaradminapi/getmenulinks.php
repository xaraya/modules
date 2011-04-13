<?php
/**
 * Get menu links 
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps Module
 * @link http://www.xaraya.com/index.php/release/eid/1033
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * This function will create the links that are shown in the admin menu
 * @author the amazonfps module development team
 * @return array The array contains the menulinks for the main menu items.
 */
function amazonfps_adminapi_getmenulinks()
{
    $menulinks = array();
    // Add a security check, so only admins can see this link
    // Hide the possible error

    if (xarSecurityCheck('viewcart',0)) {

        $menulinks[] = Array('url'   => xarModURL('amazonfps',
                                                   'admin',
                                                   'products'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View a list of products'),
                              'label' => xarML('Products'));
    }

    if (xarSecurityCheck('Adminamazonfps',0)) {
        // Add a link to the module's configuration.
        // We place this link last in the list so have a similar menu for all modules
        $menulinks[] = Array('url'   => xarModURL('amazonfps',
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Configuration'));
    }

	    if (xarSecurityCheck('Adminamazonfps',0)) {
        // Add a link to the module's configuration.
        // We place this link last in the list so have a similar menu for all modules
        $menulinks[] = Array('url'   => xarModURL('amazonfps',
                                                   'admin',
                                                   'overview'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Module Overview'),
                              'label' => xarML('Overview'),
							  'active' => array('main'));
    }

    return $menulinks;
}
?>
