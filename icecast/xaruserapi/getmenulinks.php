<?php
/**
 * File: $Id:
 * 
 * Utility function to pass individual menu items to the main menu
 * 
 * @copyright (C) 2004 by Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link 
 *
 * @subpackage icecast
 * @author Johnny Robeson 
 * @return array containing the menulinks for the main menu items.
 */
function icecast_userapi_getmenulinks()
{ 
    if (xarSecurityCheck('ViewIcecast', 0)) {
        $menulinks[] = array('url' => xarModURL('icecast', 'user', 'view'), 
            'title' => xarML('Display Currently Playing Streams'),
            'label' => xarML('Display Streams'));
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    } 

    return $menulinks;
} 

?>
