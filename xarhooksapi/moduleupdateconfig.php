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
    
    if (!xarVarFetch('fulltext_searchfields', 'pre:trim:str:1:', $searchfields, '', XARVAR_NOT_REQUIRED)) return;

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