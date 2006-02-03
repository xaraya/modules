<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function registration_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('EditRegistration',0)) {
        $menulinks[] = Array('url' => xarModURL('registration','admin','overview'),
                               'title' => xarML('Registration Overview'),
                              'label' => xarML('Overview'));

    }

    if (xarSecurityCheck('AdminRegistration',0)) {
        $menulinks[] = Array('url'   => xarModURL('registration',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the registration module configuration'),
                              'label' => xarML('Modify Config'));
    }
    return $menulinks;
}

?>