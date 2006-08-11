<?php
/**
 * Pass individual menu items to the user menu
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
 * Pass individual menu items to the user  menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function window_userapi_getmenulinks()
{ 
    
    if (xarSecurityCheck('ReadWindow',0)) {
        $menulinks[] = array('url'   => xarModURL('window', 'user', 'display'),
                             'title' => xarML('Show a display in the window'),
                             'label' => xarML('Dispay a window'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>