<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the Hitcount module development team
 * @return array Array containing the menulinks for the main menu items.
 */
function hitcount_adminapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (xarSecurityCheck('AdminHitcount', 0)) {
        $menulinks[] = Array('url'   => xarModURL('hitcount',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View hitcount statistics per module'),
                              'label' => xarML('View Statistics'));
        $menulinks[] = Array('url'   => xarModURL('hitcount',
                                                  'admin',
                                                  'hooks'),
                              'title' => xarML('Configure hitcount hooks for other modules'),
                              'label' => xarML('Enable Hooks'));
        $menulinks[] = Array('url' => xarModURL('hitcount',
                                                'admin',
                                                'modifyconfig'),
                             'title' => xarML('Modify the configuration for the Hitcount module'),
                             'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
