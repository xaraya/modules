<?php
/**
 * Update an entry in the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_updateentry()
{
    if(!xarVarFetch('id',   'int', $id   , 0, XARVAR_NOT_REQUIRED)) {return;}

    $objectid = xarModGetVar('encyclopedia','encyclopediaid');
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $objectid));

    // Get this item based on the $itemid
    // This also loads the dynamic data API, which enables the next step
    $myobject = & Dynamic_Object_Master::getObject(array('objectid' => $objectid,
                                         'moduleid' => $object['moduleid'],
                                         'itemtype' => $object['itemtype'],
                                         'itemid'   => $id));
    //Load the item info
    $myobject->getItem();
    $isvalid = $myobject->checkInput();
    $itemid = $myobject->updateItem();

    xarResponseRedirect(xarModURL('encyclopedia', 'admin', 'getrecent'));
}
?>