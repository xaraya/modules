<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
 * @return array containing the menulinks for the main menu items.
 */
function subitems_adminapi_getmenulinks()
{
    static $menulinks = array();

    if (isset($menulinks[0])) {
        return $menulinks;
    }

    if (xarSecurityCheck('AdminSubitems', 0)) {
        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'ddobjectlink_new'),
                             'title' => xarML('Add Link to DD-Objects'),
                             'label' => xarML('Add Link'),
                             'active'=> array('ddobjectlink_new')
        );
        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'ddobjectlink_view'),
                             'title' => xarML('View Links to DD-Objects'),
                             'label' => xarML('View Links'),
                             'active'=> array('ddobjectlink_view',
                                              'ddobjectlink_edit',
                                              'ddobjectlink_delete')
        );
        $menulinks[] = Array('url' => xarModURL('subitems',
                                                'admin',
                                                'modifyconfig'),
                            'title' => xarML('Modify the configuration for the module'),
                            'label' => xarML('Modify Config'),
                             'active'=> array('modifyconfig')
        );
    }
    $menulinks[] = Array('url' => xarModURL('subitems',
                                            'admin',
                                            'overview'),
                         'active'=> array('overview'),
                         'title' => xarML('Introduction on handling this module'),
                         'label' => xarML('Overview')
    );

    return $menulinks;
}

?>
