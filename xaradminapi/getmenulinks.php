<?php
/**
 * Pass menu items for admin to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xarlinkme_adminapi_getmenulinks()
{ 

    // Security Check
    if (xarSecurityCheck('AdminxarLinkMe', 0)) {
                                                   
        $menulinks[] = Array('url' => xarModURL('xarlinkme','admin','banneradmin'),
                             'title' => xarML('Manage Client Banners'),
                             'label' => xarML('Client Banners'));
       $menulinks[] = Array('url' => xarModURL('xarlinkme','admin','linkadmin'),
                             'title' => xarML('Manage Site Banners and Links'),
                             'label' => xarML('Site Banner Links'));
        $menulinks[] = Array('url' => xarModURL('xarlinkme','admin','modifyconfig'),
                             'title' => xarML('Configure the Link Me module'),
                             'label' => xarML('Modify Config'));
     }

    /* If we return nothing, then we need to tell PHP this, in order to 
       avoid an ugly E_ALL error.
    */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     to the main menu for display.
     */
    return $menulinks;
} 

?>