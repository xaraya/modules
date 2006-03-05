<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author mikespub
 * @return array containing the menulinks for the main menu items.
 */
function changelog_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminChangeLog')) {
        $menulinks[] = Array('url'   => xarModURL('changelog',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View changelog entries per module'),
                              'label' => xarML('View Changes'));
        $menulinks[] = Array('url'   => xarModURL('changelog',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the changelog configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
