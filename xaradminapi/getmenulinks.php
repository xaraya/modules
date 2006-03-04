<?php
/**
* Get module links for admin menu
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the eBulletin module development team
 * @return array containing the menulinks for the main menu items.
 */
function ebulletin_adminapi_getmenulinks()
{
    $menulinks = array();
    // view publications and issues and subscribers
    if (xarSecurityCheck('EditeBulletin', 0)) {
        $menulinks[] = array(
            'url' => xarModURL('ebulletin', 'admin', 'view'),
            'title' => xarML('View all publications.'),
            'label' => xarML('View publications')
        );

        $menulinks[] = array(
            'url' => xarModURL('ebulletin', 'admin', 'viewissues'),
            'title' => xarML('View all issues.'),
            'label' => xarML('View issues')
        );

        $menulinks[] = array(
            'url' => xarModURL('ebulletin', 'admin', 'viewsubscribers'),
            'title' => xarML('View all subscribers.'),
            'label' => xarML('View subscribers')
        );
    }

    // modify config
    if (xarSecurityCheck('AdmineBulletin', 0)) {
        $menulinks[] = array(
            'url' => xarModURL('ebulletin', 'admin', 'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config')
        );
    }

    return $menulinks;
}

?>
