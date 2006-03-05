<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Ephemerids module development team
 * @return array containing the menulinks for the main menu items.
 */
function ephemerids_adminapi_getmenulinks()
{

// Security Check
    if (xarSecurityCheck('AddEphemerids',0)) {

        $menulinks[] = Array('url'   => xarModURL('ephemerids',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new ephemerids into the system'),
                              'label' => xarML('Add'));
    }

// Security Check
    if (xarSecurityCheck('EditEphemerids',0)) {

        $menulinks[] = Array('url'   => xarModURL('ephemerids',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and Edit Ephemerids'),
                              'label' => xarML('View'));
    }

// Security Check
    if (xarSecurityCheck('AdminEphemerids',0)) {
        $menulinks[] = Array('url'   => xarModURL('ephemerids',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the Ephemerids'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>