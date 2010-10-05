<?php
/**
 * ItemDelete Hook
 *
 * Deletes db info for deleted item
**/
function fulltext_hooksapi_itemdelete($args)
{
    extract($args);

    if (empty($extrainfo)) $extrainfo = array();
    
    if (empty($module)) {
        if (!empty($extrainfo['module'])) {
            $module = $extrainfo['module'];
        } else {
            list($module) = xarController::$request->getInfo();
        }
    }

    $module_id = xarMod::getRegID($module);
    if (!$module_id) return;

    if (empty($itemtype)) {
        if (!empty($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        } else {
            $itemtype = null;
        }
    }
    if (!empty($itemtype) && !is_numeric($itemtype))
        throw new BadParameterException('itemtype');

    if (empty($objectid)) {
        if (!empty($extrainfo['itemid'])) {
            $objectid = $extrainfo['itemid'];
        }
    }
    if (empty($objectid) || !is_numeric($objectid))
        throw new BadParameterException('objectid');

    // make sure we have an item to delete 
    $item = xarMod::apiFunc('fulltext', 'user', 'getitem',
        array(
            'module_id' => $module_id,
            'itemtype' => $itemtype,
            'itemid' => $objectid,
        ));
    if (empty($item))
        throw new NotFoundException($objectid);
        
    if (!xarMod::apiFunc('fulltext', 'user', 'deleteitem',
        array(
            'id' => $item['id'],
        ))) return;
        
    $extrainfo['fulltext_id'] = $item['id'];

    return $extrainfo;
}
?>