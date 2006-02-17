<?php
/**
 * Modify a Dyn Data item from SIGMAPersonnel
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel Module Development Team
 */
/**
 * Modify an item of the itemtype specified
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param $itemtype - type of item that is being created (required)
 * @param $itemid - item id  (required)
 * @param $objectid - object id is used instead of item id if there is one
 * @return template data
 */
function sigmapersonnel_admin_modify($args)
{
    if (!xarVarFetch('itemid',   'id', $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'id', $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('objectid', 'id', $objectid,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'sigmapersonnel');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'modify', 'sigmapersonnel');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!xarSecurityCheck('AdminSIGMAPersonnel',1)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sigmapersonnel',
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    $data['menu']      = xarModFunc('sigmapersonnel','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('Modify a hooked dynamic data object'));

    // Get data ready for the template
    $data['itemid']   = $itemid;
    $data['itemtype'] = $itemtype;
    $data['object']   = $object;

    // Take care of hooks
    $item = array();
    $item['module']   = 'sigmapersonnel';
    $item['itemid']   = $itemid;
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item','modify',$itemid,$item);
    if (empty($hooks)) {
        $data['hooks'] = array();
    }else {
        $data['hooks'] = $hooks;
    }
    // Authentication
    $data['authid'] =xarSecGenAuthKey();
    // Return the template variables defined in this function
    return $data;
}
?>