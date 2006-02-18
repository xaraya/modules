<?php
/**
 * AddressBook user confirmDelete
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Confirm deletion
 * @return array
 */
function addressbook_user_confirmdelete()
{
    $output = array();

    // preserve menu settings
    $menuValues = xarModAPIFunc('addressbook','user','getmenuvalues');
    foreach ($menuValues as $key=>$value) {
        $output[$key] = $value;
    }

    $output['menuValues']=array('catview'   =>$output['catview'],
                    'menuprivate'=>$output['menuprivate'],
                    'all'       =>$output['all'],
                    'sortview'  =>$output['sortview'],
                    'page'      =>$output['page'],
                    'char'      =>$output['char'],
                    'total'     =>$output['total']);

    // Get the values
    $output = xarModAPIFunc('addressbook','user','getsubmitvalues', array ('output'=>$output));

    // Get detailed values from database
    $details = xarModAPIFunc('addressbook','user','getdetailvalues',array('id'=>$output['id']));
    foreach ($details as $key=>$value) {
        $output[$key] = $value;
    }

    $output['authid'] = xarSecGenAuthKey();
    $output['id'] = $output['id'];
    $output['confirmDeleteTEXT'] = xarML('Delete this Address Book item?');
    $output['buttonDelete'] = xarML('Delete');
    $output['buttonCancel'] = xarML('Cancel');

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END confirmDelete

?>