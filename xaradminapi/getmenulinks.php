<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function stats_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminStats', 0)) {
        $menulinks[] = Array('url' => xarModURL('stats',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the stats module'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}
?>
