<?php
/**
 * Delete a Dyn data item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls Module Development Team
 */
/**
 * Delete an item
 *
 * @param int 'itemid' the id of the item to be deleted
 * @param string 'confirm' confirm that this item can be deleted
 * @return bool true of success false on failure
 */
function maxercalls_admin_delete($args)
{
    // Get Vars
    if (!xarVarFetch('itemid',    'id',    $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',  'id',    $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('objectid',  'id',    $objectid,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('noconfirm', 'isset', $noconfirm, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('confirm',   'isset', $confirm,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if( $noconfirm )
    {
        xarResponseRedirect(xarModURL('maxercalls', 'admin', 'view',
                                      array('itemtype' => $itemtype)
                                     )
                           );
    }

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'delete', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item type', 'admin', 'delete', 'maxercalls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module'   => 'maxercalls',
                                   'itemtype' => $itemtype,
                                   'itemid'   => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    // Security check
    if (!xarSecurityCheck('AdminMaxercalls',1,'item',$itemid)) return;

    //$data['menu']      = xarModFunc('maxercalls','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('Remove this dynamic data object'));

    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user

        // Specify for which item you want confirmation
        $data['itemid']   = $itemid;
        $data['itemtype'] = $itemtype;
        $data['object']   = $object;

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action
    // so check the Auth Key
    if (!xarSecConfirmAuthKey()) return;

    $itemid = $object->deleteItem();
    if (empty($itemid)) return;

    // Return
    return xarModURL('maxercalls', 'admin', 'view', array('itemtype' => $itemtype));//true;
}

?>
