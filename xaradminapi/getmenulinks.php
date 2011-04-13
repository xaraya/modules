<?php
/**
 * Get menu links
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/1049.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * This function will create the links that are shown in the admin menu
 * @author the Contact Form module development team
 * @return array The array contains the menulinks for the main menu items.
 */
function contactform_adminapi_getmenulinks()
{
    $menulinks = array();
    // Add a security check, so only admins can see this link
    // Hide the possible error

    if (xarSecurityCheck('EditContactForm',0)) {

        $menulinks[] = Array('url'   => xarModURL('contactform',
                                                   'admin',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all example items that have been added.'),
                              'label' => xarML('List Items'));
    }

    if (xarSecurityCheck('AdminContactForm',0)) {
        // Add a link to the module's configuration.
        // We place this link last in the list so have a similar menu for all modules
        $menulinks[] = Array('url'   => xarModURL('contactform',
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Configuration'));
    }

	if (xarSecurityCheck('AdminContactForm', 0)) {
        $menulinks[] = array(
            'url'      => xarModURL('contactform', 'admin', 'overview'),
            'title'    => 'Module Overview',
            'label'    => 'Overview',
			'active' => array('main'));
    } 

    return $menulinks;
}
?>
