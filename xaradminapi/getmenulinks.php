<?php
/**
 * Get menu links
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function accessmethods_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('AdminAccessMethods',0)) {

        $menulinks[] = Array('url'   => xarModURL('accessmethods',
                                                   'admin',
                                                   'overview'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));
    }

    if (xarSecurityCheck('AddAccessMethods',0)) {

        $menulinks[] = Array('url'   => xarModURL('accessmethods',
                                                   'admin',
                                                   'new'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Adds a new site to manage.'),
                              'label' => xarML('Add Access Method'));
    }

    if (xarSecurityCheck('EditAccessMethods',0)) {

        $menulinks[] = Array('url'   => xarModURL('accessmethods',
                                                   'admin',
                                                   'view'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('View all sites that have been added.'),
                              'label' => xarML('List Access Methods'));
    }

    if (xarSecurityCheck('AdminAccessMethods',0)) {

        $menulinks[] = Array('url'   => xarModURL('accessmethods',
                                                   'admin',
                                                   'modifyconfig'),
                              // In order to display the tool tips and label in any language,
                              // we must encapsulate the calls in the xarML in the API.
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
