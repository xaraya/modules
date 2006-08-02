<?php
/**
 * Pass individual menu items to the admin menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 */

/**
 * Pass individual menu items to the admin  menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function window_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminWindow',0)) {
        $menulinks[] = array('url'   => xarModURL('window',
                                                  'admin',
                                                  'newurl'),
                              'title' => xarML('Specify settings for different window displays'),
                              'label' => xarML('Manage Displays'));
    }
    if (xarSecurityCheck('AddWindow',0)) {
        $menulinks[] = array('url'   => xarModURL('window',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>