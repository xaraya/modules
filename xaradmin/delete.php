<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
   Delete an item

   @param 'itemid' the id of the item to be deleted
   @param 'confirm' confirm that this item can be deleted
   @return true of success
           false on failure
*/
function helpdesk_admin_delete($args)
{
    if( !Security::check(SECURITY_ADMIN, 'helpdesk') ){ return false; }

    // Get Vars
    if (!xarVarFetch('itemid',    'id',    $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',  'id',    $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('objectid',  'id',    $objectid,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('noconfirm', 'isset', $noconfirm, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('confirm',   'isset', $confirm,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if( $noconfirm )
    {
        xarResponseRedirect(xarModURL('helpdesk', 'admin', 'view',
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
                    'item id', 'admin', 'delete', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item type', 'admin', 'delete', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module'   => 'helpdesk',
                                   'itemtype' => $itemtype,
                                   'itemid'   => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  However,
    // in this case we had to wait until we could obtain the item name to
    // complete the instance information so this is the first chance we get to
    // do the check
    if (!xarSecurityCheck('deletehelpdesk',1,'item',$itemid)) return;

    $data['menu']      = xarModFunc('helpdesk','admin','menu');
    $data['menutitle'] = xarModAPIFunc('helpdesk','admin','menu');

    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user

        // Specify for which item you want confirmation
        $data['itemid']   = $itemid;
        $data['itemtype'] = $itemtype;
        $data['object']   =& $object;

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action
    // so check the Auth Key
    if (!xarSecConfirmAuthKey()) return;

    $itemid = $object->deleteItem();
    if (empty($itemid)) return;

    xarResponseRedirect(xarModURL('helpdesk', 'admin', 'view', array('itemtype' => $itemtype)));

    // Return
    return true;
}

?>
