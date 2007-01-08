<?php
/**
 * Standard function to modify a dynamic item in Maxercalls
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @author Maxercalls module development team
 */
/**
 * Modify an item of the itemtype specified
 *
 * @param $itemtype - type of item that is being created (required)
 * @param $itemid - item id  (required)
 * @param $objectid - object id is used instead of item id if there is one
 * @return template data
 */
function maxercalls_admin_modify($args)
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
                    'item id', 'admin', 'modify', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'modify', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!xarSecurityCheck('AdminMaxercalls',1,'item',$itemid)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'maxercalls',
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;
    // Authentication
    $data['authid'] = xarSecGenAuthKey();
    // Get data ready for the template
    $data['itemid']   = $itemid;
    $data['itemtype'] = $itemtype;
    $data['object']   = $object;

    // Take care of hooks
    $item = array();
    $item['module']   = 'maxercalls';
    $item['itemid']   = $itemid;
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item','modify',$itemid,$item);
    if (empty($hooks)) {
        $data['hooks'] = array();
    }else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}
?>
