<?php
/**
 * Add an entry to the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_addentry()
{
//    if (!xarSecurityCheck('AddEncyclopedia',0,'Entry',$item['term'] . "::" . $id)) {return;}
    if (!xarVarFetch('itemid',      'isset', $itemid,     0, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    $objectid = xarModGetVar('encyclopedia','encyclopediaid');
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $objectid));

    // Get this item based on the $itemid
    // This also loads the dynamic data API, which enables the next step
    $myobject = & Dynamic_Object_Master::getObject(array('objectid' => $objectid,
                                         'moduleid' => $object['moduleid'],
                                         'itemtype' => $object['itemtype'],
                                         'itemid'   => $itemid));

    $isvalid = $myobject->checkInput();
    $itemid = $myobject->createItem();
    if (empty($itemid)) return; // throw back

    xarResponseRedirect(xarModURL('encyclopedia', 'admin', 'newentry'));
}
?>