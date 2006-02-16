<?php
/**
 * Modify DD item for courses
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
 * Modify an item of the itemtype specified
 *
 * @param $itemtype - type of item that is being created (required)
 * @param $itemid - item id  (required)
 * @param $objectid - object id is used instead of item id if there is one
 * @return array with template data
 */
function courses_admin_modify($args)
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
                    'item id', 'admin', 'modify', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'modify', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!xarSecurityCheck('EditCourses',1)) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'courses',
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    $data['menu']      = xarModFunc('courses','admin','menu');
    $data['menutitle'] = xarML('Modify Parameter');

    // Get data ready for the template
    $data['itemid']   = $itemid;
    $data['itemtype'] = $itemtype;
    $data['object']   =& $object;

    // Take care of hooks
    // Are these needed?
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
    // Authentication
    $data['authid'] =xarSecGenAuthKey();
    // Return the template variables defined in this function
    return $data;
}
?>
