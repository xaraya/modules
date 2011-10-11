<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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