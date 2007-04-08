<?php
/**
 * Webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the webshare module development team
 * @return array containing the menulinks for the main menu items.
 */
function webshare_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('Adminwebshare')) {
        $menulinks[] = Array('url'   => xarModURL('webshare',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the webshare module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
