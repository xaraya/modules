<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the subitems module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function subitems_adminapi_getmenulinks()
{
    $menulinks = array();

// TODO: distinguish between edit/add/delete/admin if necessary
    if (xarSecurityCheck('AdminSubitems', 0)) {
        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'ddobjectlink_new'),
                             'title' => xarML('Add Link to DD-Objects'),
                             'label' => xarML('Add Link'));

        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'ddobjectlink_view'),
                             'title' => xarML('View Links to DD-Objects'),
                             'label' => xarML('View Links'));

        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'modifyconfig'),
                            'title' => xarML('Modify the configuration for the module'),
                            'label' => xarML('Modify Config'));
    }

    return $menulinks;
}

?>
