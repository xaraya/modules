<?php
/**
 * ItemUpdate Hook
 *
 * Updates item text
**/
function fulltext_hooksapi_itemupdate($args)
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

    // get settings for current module
    $varname = 'fulltext_settings';
    // optionally for current itemtype
    if (!empty($itemtype))
        $varname .= '_' . $itemtype;    
    $settings = xarModVars::get($module, $varname);
    
    // fall back to current module defaults if settings are empty
    if (empty($settings) && !empty($itemtype))
        $settings = xarModVars::get($module, 'fulltext_settings');
    
    // fall back to fulltext module defaults if settings are still empty
    if (empty($settings))
        $settings = xarModVars::get('fulltext', 'fulltext_settings');
    
    $settings = @unserialize($settings);    
    
    if (!empty($settings['searchfields'])) {
        if (strpos($settings['searchfields'], ',') !== false) {
            $searchfields = explode(',', $settings['searchfields']);
        } else {
            $searchfields = array($settings['searchfields']);
        }
    }
    
    $text = "";
    if (!empty($searchfields)) {
        $numfields=count($searchfields);
        $i = 1;
        foreach ($searchfields as $toindex) {
            $toindex = trim($toindex);
            if (isset($extrainfo[$toindex])) {
                $text .= $extrainfo[$toindex];
                if ($i < $numfields)                 
                    $text .= " ";
            }
            $i++;
        }
    }   
    
    $text = strip_tags($text);
    
    // make sure we have an item to update 
    $item = xarMod::apiFunc('fulltext', 'user', 'getitem',
        array(
            'module_id' => $module_id,
            'itemtype' => $itemtype,
            'itemid' => $objectid,
        ));

    // fulltext module must have been hooked since this item was created, create it now
    if (empty($item)) {
        $item = xarMod::apiFunc('fulltext', 'user', 'createitem',
            array(
                'module_id' => $module_id,
                'itemtype' => $itemtype,
                'itemid' => $objectid,
                'text' => $text,
            ));
    } else {
        // Store the updated text for this item
        if (!xarModAPIFunc('fulltext', 'user', 'updateitem',
            array(
                'id' => $item['id'],
                'text' => $text,
            ))) return;
    }
    $extrainfo['fulltext_id'] = $item['id'];
    
    return $extrainfo;
}
?>