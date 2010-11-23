<?php
function twitter_hooksapi_moduleupdateconfig($args)
{
    extract($args);

    if (empty($extrainfo)) $extrainfo = array();
    if (!is_array($extrainfo))
        $invalid[] = 'extrainfo';    
    
    if (empty($module)) {
        if (isset($extrainfo['module'])) {
            $module = $extrainfo['module'];
        } elseif (!empty($objectid) && is_string($objectid)) {
            $module = $objectid;
        } else {
            list($module) = xarRequest::getInfo();
        }
    }
    $module_id = xarMod::getRegID($module);
    if (!$module_id) 
        $invalid[] = 'module';
    /*
    if (empty($itemtype)) {
        if (isset($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        } else {
            $itemtype = null;
        }
    }
    if (!empty($itemtype) && !is_numeric($itemtype))
        $invalid[] = 'itemtype';
    */  
    if (!empty($invalid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'hooksapi', 'moduleupdateconfig', 'Twitter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return $extrainfo;
    }
    $modvar = "hooks_{$module}";    
    xarModVars::delete('twitter', $modvar);
    $extrainfo['twitter_moduleremove'] = $module;
    
    return $extrainfo;    
    
}
?>