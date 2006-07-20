<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
/**
 * Utility function pass individual menu items to the main menu
 *
 * @public
 * @author Richard Cave
 * @return array containing the menulinks for the main menu items.
 */
function sniffer_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AdminSniffer')) {
        $menulinks[] = Array('url'   => xarModURL('sniffer',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View browser and OS of visitors.'),
                              'label' => xarML('View Sniffs'));

        // Check that the GD library is available
        if (extension_loaded('gd')) {
            $menulinks[] = Array('url'   => xarModURL('sniffer',
                                                      'admin',
                                                      'chart'),
                                 'title' => xarML('Chart browser and OS of visitors.'),
                                 'label' => xarML('Chart Sniffs'));
        }
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
