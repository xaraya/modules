<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 * @link http://xaraya.com/index.php/release/27.html
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @return array Array containing the menulinks for the main menu items.
 */
function authentication_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('EditAuthentication',0)) {
        $menulinks[] = Array('url' => xarModURL('authentication','admin','overview'),
                               'title' => xarML('Authentication Overview'),
                              'label' => xarML('Overview'));

    }

    if (xarSecurityCheck('AdminAuthentication',0)) {
        $menulinks[] = Array('url'   => xarModURL('authentication',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the authentication module configuration'),
                              'label' => xarML('Modify Config'));
    }
    return $menulinks;
}

?>