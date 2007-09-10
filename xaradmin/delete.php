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
 * delete a logger
 * @param 'itemid' the id of the item to be deleted
 * @param 'confirm' confirm that this item can be deleted
 */
function logconfig_admin_delete($args)
{
    // Get parameters from whatever input we need.
    extract($args);

    if (!xarVarFetch('itemid',   'id', $itemid,  $itemid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid,    $objectid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'id', $itemtype,    $itemtype,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'string',  $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'update', 'logconfig');
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

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
    if (!xarSecurityCheck('AdminLogConfig')) return;

    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user

        $data = xarModAPIFunc('logconfig','admin','menu');

        // Specify for which item you want confirmation
        $data['itemid'] = $itemid;
        $data['itemtype'] = $itemtype;
        $data['object'] =& $object;

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    if (!xarSecConfirmAuthKey()) return;

    $itemid = $object->deleteItem();
    if (empty($itemid)) return;

    xarResponseRedirect(xarModURL('logconfig', 'admin', 'view'));

    // Return
    return true;
}

?>