<?php
/**
 * Update a DD item for this module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Update an item of a specified item type
 *
 * @param $itemtype - type of item that is being updated (required)
 * @param $itemid - item id  (required)
 * @param $preview  - do a preview if set (optional)
 * @return true on success
 *         false on failure
 */
function courses_admin_update($args)
{
    xarVarFetch('itemid',   'id',   $itemid,   NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'id',   $itemtype, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('objectid', 'id',   $objectid, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('preview',  'isset',$preview,  NULL, XARVAR_NOT_REQUIRED);

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item id', 'admin', 'update', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'update', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module'   => 'courses',
                                   'itemtype' => $itemtype,
                                   'itemid'   => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    // check the input values for this object
    $isvalid = $object->checkInput();
    /* Get menu elements */
    $data['menu']      = xarModFunc('courses','admin','menu');
    $data['menutitle'] = xarML('Modify course parameter');

    // if we're in preview mode, or if there is some invalid input, show the form again
    if (!empty($preview) || !$isvalid) {
        $data = xarModAPIFunc('courses','admin','menu');

        // Get template data together for preview
        $data['object']   = & $object;
        $data['itemid']   = $itemid;
        $data['itemtype'] = $itemtype;
        $data['preview']  = $preview;

        // Let's take care of the hooks
        $item = array();
        $item['module']   = 'courses';
        $item['itemid']   = $itemid;
        $item['itemtype'] = $itemtype;
        $hooks = xarModCallHooks('item','modify',$itemid,$item);
        if (empty($hooks)) {
            $data['hooks'] = array();
        }else {
            $data['hooks'] = $hooks;
        }
        /* Authentication */
        $data['authid'] = xarSecGenAuthKey();
        // Empty status message
        xarSessionSetVar('statusmsg', '');
        // Return the template variables defined in this function
        return xarTplModule('courses','admin','modify', $data);
    }

    // update the item
    $itemid = $object->updateItem();
    if (empty($itemid)) return; // throw back
    // let's go back to the admin view
    xarSessionSetVar('statusmsg', xarML('Item updated'));
    // Why does this one not work correct?
    xarResponseRedirect(xarModURL('courses', 'admin', 'view', array('itemtype' => $itemtype)));

    // Return
    return true;
}
?>
