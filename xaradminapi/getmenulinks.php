<?php
/**
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */


/**
 * Return array of menu information.
 * 
 * @return array containing the menulinks for the main menu items.
 */
function ie7_adminapi_getmenulinks()
{ 
    if (!xarSecurityCheck('AdminIE7',0)) return;    
    /* locals */
    $menulinks = array();
    $menulinks[] = array('url' => xarModURL('ie7', 'admin', 'test'),
            'title' => xarML('Test ie7'),
            'label' => xarML('Test Setup'));
    $menulinks[] = array('url' => xarModURL('ie7', 'admin', 'modifyconfig'),
            'title' => xarML('Change the ie7 module settings'),
            'label' => xarML('Modify config'));

    return $menulinks;
} 

?>
