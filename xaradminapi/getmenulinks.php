<?php
/**
 * Get admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Lists module development team
 * @return array containing the menulinks for the main menu items.
 */
function lists_adminapi_getmenulinks()
{
    // Security Check
    //if (xarSecurityCheck('EditLists',0)) {
        $menulinks[] = array(
            'url'   => xarModURL('lists', 'admin', 'view'),
            'title' => xarML('View lists'),
            'label' => xarML('View lists'));
    //}
    // Security Check
    //if (xarSecurityCheck('EditLists',0)) {
        $menulinks[] = array(
            'url'   => xarModURL('lists', 'admin', 'modifyconfig'),
            'title' => xarML('Modify Configuration'),
            'label' => xarML('Modify Config'));
    //}

    if (empty($menulinks)){
        $menulinks = array();
    }

    return $menulinks;
}
?>