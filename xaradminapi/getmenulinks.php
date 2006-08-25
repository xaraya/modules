<?php
/**
 * Utility function to pass menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @return array containing the menulinks for the main menu items.
 */
function search_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminSearch', 0)) {

        $menulinks[] = Array('url' => xarModURL('search','admin', 'modifyconfig'),
            'title' => xarML('Modify the configuration of Search display'),
            'label' => xarML('Modify Config'));
    }
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    // return values back to the main menu for display.
    return $menulinks;
}

?>
