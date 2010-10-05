<?php
/**
 * ModuleModifyconfig Hook
 *
 * Displays search configuration options in subject module (+itemtype) modifyconfig UI
**/
function fulltext_hooks_modulemodifyconfig($args)
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
    
    if (!is_array($settings))
        $settings = array(
            'searchfields' => '',
        );
   
    $data = $settings;
    $data['searchmodule'] = $module;
    $data['searchitemtype'] = $itemtype;
    
    return xarTplModule('fulltext', 'hooks', 'modulemodifyconfig', $data);
}
?>