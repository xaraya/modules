<?php
/**
 * Delete a term from the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_deleteentry()
{
//    if (!xarSecurityCheck('EditEncyclopedia',0,'Item',"$item[name]::$id")) {
    if (!xarVarFetch('itemid',    'int', $itemid,  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('confirmed', 'int', $confirmed, NULL, XARVAR_DONT_SET)) return;

    // Get the ID of the encyclopedia object
    $objectid = xarModGetVar('encyclopedia','encyclopediaid');

    // Get this item based on the $id
    // This also loads the dynamic data API, which enables the next step
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $objectid));

    // Get this item based on the $id
    $myobject =& Dynamic_Object_Master::getObject(array('objectid' => $objectid,
                                         'moduleid' => $object['moduleid'],
                                         'itemtype' => $object['itemtype'],
                                         'itemid'   => $itemid));

    $myobject->getItem();
    // Check for confirmation.
    if (empty($confirmed)) {
        // Send the name of the item to the template for a nice message
        $item = $myobject->properties['term'];
        $data['itemname'] = $item->value;
        $data['itemid'] = $itemid;
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // The deletion has been confirmed
    if (!xarSecConfirmAuthKey()) return;
    $itemid = $myobject->deleteItem();
    xarResponseRedirect(xarModURL('encyclopedia', 'admin', 'getrecent'));
}
?>