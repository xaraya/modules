<?php
/**
 * 
 * Standard function to delete a DD item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team
 */

/**
   Delete an item

   @param 'itemid' the id of the item to be deleted
   @param 'confirm' confirm that this item can be deleted
   @return true of success
           false on failure
*/
function courses_admin_delete($args)
{
    // Get Vars
    if (!xarVarFetch('itemid',    'id',    $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype',  'id',    $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('objectid',  'id',    $objectid,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('noconfirm', 'isset', $noconfirm, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('confirm',   'isset', $confirm,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if( $noconfirm )
    {
        xarResponseRedirect(xarModURL('courses', 'admin', 'view',
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
                    'item id', 'admin', 'delete', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item type', 'admin', 'delete', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('module'   => 'courses',
                                   'itemtype' => $itemtype,
                                   'itemid'   => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    // Security check
    if (!xarSecurityCheck('AdminCourses')) return;

    $data['menu']      = xarModFunc('courses','admin','menu');
    $data['menutitle'] = xarModAPIFunc('courses','admin','menu');

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

    xarResponseRedirect(xarModURL('courses', 'admin', 'view', array('itemtype' => $itemtype)));

    // Return
    return true;
}

?>
