<?php
/**
 * ModuleUpdateconfig Hook
 *
 * Updates subject module (+itemtype) search configuration 
**/
function fulltext_hooksapi_moduleupdateconfig($args)
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
    
    if (!xarVarFetch('fulltext_searchfields', 'isset', $searchfields, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($searchfields)) {
        if (!is_array($searchfields))
            $searchfields = strpos($searchfields, ',') === false ? array($searchfields) : array_map('trim', explode(',', $searchfields));
        try {
            $itemfields = xarMod::apiFunc($module, 'user', 'getitemfields',
                array('module' => $module, 'itemtype' => $itemtype));
        } catch (Exception $e) {
            $itemfields = array();
        }
        if (!empty($itemfields))
            $searchfields = array_intersect(array_flip($itemfields), $searchfields); 
        
        $searchfields = join(',', $searchfields);    
    }

    // set settings for current module
    $varname = 'fulltext_settings';
    // optionally for current itemtype
    if (!empty($itemtype))
        $varname .= '_' . $itemtype;    
    
    $settings = array(
        'searchfields' => $searchfields,
    );      
    
    xarModVars::set($module, $varname, serialize($settings));
    
    $extrainfo['fulltext_searchfields'] = $searchfields;   
    
    return $extrainfo;
}
?>