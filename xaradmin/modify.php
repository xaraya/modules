<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * modify an item
 */
function logconfig_admin_modify($args)
{
    extract($args);

    if (!xarVarFetch('itemid',   'id',   $itemid)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'id',     $itemtype, $itemtype, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'logconfig');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'logconfig',
                                   'itemid' => $itemid,
                                   'itemtype' => $itemtype));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    if (!xarSecurityCheck('AdminLogConfig')) return;

    $data = xarModAPIFunc('logconfig','admin','menu');
    $data['itemid'] = $itemid;
    $data['itemtype'] = $itemtype;
    $data['object'] =& $object;

    // Return the template variables defined in this function
    return $data;
}

?>