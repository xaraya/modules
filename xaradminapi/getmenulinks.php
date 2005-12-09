<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @author the xarDPLink module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xardplink_adminapi_getmenulinks()
{
    /* Show an overview menu option here if you like */
    if (xarSecurityCheck('AdminXardplink', 0)) {
    $menulinks[] = Array('url' => xarModURL('xardplink','admin','main'),
            'title' => xarML('xarDPLink Overview'),
            'label' => xarML('Overview'));
    }
    /* Security Check */
    if (xarSecurityCheck('AdminXardplink', 0)) {
        $menulinks[] = Array('url' => xarModURL('xardplink','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>