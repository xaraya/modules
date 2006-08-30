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
   Update an item of a specified item type

   @param $itemtype - type of item that is being updated (required)
   @param $itemid - item id  (required)
   @param $preview  - do a preview if set (optional)
   @return true on success
           false on failure
*/
function helpdesk_admin_update($args)
{
    if( !Security::check(SECURITY_ADMIN, 'helpdesk') ){ return false; }

    xarVarFetch('itemid',   'id', $itemid,      NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'id', $itemtype,    NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('objectid', 'id', $objectid,    NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('preview',  'isset', $preview,  NULL, XARVAR_NOT_REQUIRED);

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item id', 'admin', 'update', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'update', 'helpdesk');
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

    // check the input values for this object
    $isvalid = $object->checkInput();

    // if we're in preview mode, or if there is some invalid input, show the form again
    if (!empty($preview) || !$isvalid) {
        $data = xarModAPIFunc('helpdesk','admin','menu');

        // Get template data together for preview
        $data['object']   = & $object;
        $data['itemid']   = $itemid;
        $data['itemtype'] = $itemtype;
        $data['preview']  = $preview;

        // Let's take care of the hooks
        $item = array();
        $item['module']   = 'helpdesk';
        $item['itemid']   = $itemid;
        $item['itemtype'] = $itemtype;
        $hooks = xarModCallHooks('item','modify',$itemid,$item);
        if (empty($hooks)) {
            $data['hooks'] = array();
        }else {
            $data['hooks'] = $hooks;
        }

        // Return the template variables defined in this function
        return xarTplModule('helpdesk','admin','modify', $data);
    }

    // update the item
    $itemid = $object->updateItem();
    if (empty($itemid)) return; // throw back

    // Let's take care of the hooks
    $item = array();
    $item['module']   = 'helpdesk';
    $item['itemid']   = $itemid;
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item','update',$itemid,$item);
    if (empty($hooks)) {
    $data['hooks'] = array();
    }else {
    $data['hooks'] = $hooks;
    }

    // let's go back to the admin view
    xarResponseRedirect(xarModURL('helpdesk', 'admin', 'view', array('itemtype' => $itemtype)));

    // Return
    return true;
}
?>
