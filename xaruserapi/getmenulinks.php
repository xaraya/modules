<?php
/**
 * File: $Id: getmenulinks.php,v 1.3 2004/11/16 05:40:47 garrett Exp $
 *
 * AddressBook user getMenuLinks
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * builds an array of menulinks for display in a menu block
 *
 * @return array of menu links
 */
function addressbook_userapi_getmenulinks()
{
    // FIXME:<garrett> should be able to move this all into Xaraya sec model
    if (xarModAPIFunc(__ADDRESSBOOK__,'user','checkaccesslevel',array('option'=>'create'))) {

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'user',
                                                   'insertedit'),
                              'title' => xarML('Add a new address'),
                              'label' => xarML('New Address'));
    }

    if (xarSecurityCheck('ViewAddressBook',0)) {

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'user',
                                                   'viewall'),
                              'title' => xarML('View address book entries'),
                              'label' => xarML('View Addresses'));
    }
/**
 * TODO: this is v1.3.1 functionality

    if (xarSecurityCheck('AdminAddressBook',0)) {

        $menulinks[] = Array('url'   => xarModURL(__ADDRESSBOOK__,
                                                   'user',
                                                   'export'),
                             'title' => xarML('Export address book entries'),
                             'label' => xarML('Export Addresses'));
    }
*/
    return $menulinks;

} // END getMenuLinks

?>