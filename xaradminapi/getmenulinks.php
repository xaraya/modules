<?php
/**
 * Chat Module - Port of PJIRC for Xaraya
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Chat Module
 * @link http://xaraya.com/index.php/release/158.html
 * @author John Cox
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Chat module development team
 * @return array Array containing the menulinks for the main menu items.
 */
function chat_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AdminChat', 0)) {
        $menulinks[] = Array('url'   => xarModURL('chat',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for chat'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>