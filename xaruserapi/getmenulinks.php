<?php
/**
 * AddressBook user getMenuLinks
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    if (xarModAPIFunc('addressbook','user','checkaccesslevel',array('option'=>'create'))) {

        $menulinks[] = Array('url'   => xarModURL('addressbook',
                                                   'user',
                                                   'insertedit'),
                              'title' => xarML('Add a new address'),
                              'label' => xarML('New Address'));
    }

    if (xarSecurityCheck('ReadAddressBook',0)) {

        $menulinks[] = Array('url'   => xarModURL('addressbook',
                                                   'user',
                                                   'viewall'),
                              'title' => xarML('View address book entries'),
                              'label' => xarML('View Addresses'));
    }
/**
 * TODO: this is v1.3.1 functionality

    if (xarSecurityCheck('AdminAddressBook',0)) {

        $menulinks[] = Array('url'   => xarModURL('addressbook',
                                                   'user',
                                                   'export'),
                             'title' => xarML('Export address book entries'),
                             'label' => xarML('Export Addresses'));
    }
*/
    return $menulinks;

} // END getMenuLinks

?>