<?php
function twitter_hooks_modulemodifyconfig($args)
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
            list($module) = xarRequestGetInfo();
        }
    }
    $module_id = xarModGetIDFromName($module);
    if (!$module_id) 
        $invalid[] = 'module';

    if (empty($itemtype)) {
        if (isset($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        } else {
            $itemtype = null;
        }
    }
    if (!empty($itemtype) && !is_numeric($itemtype))
        $invalid[] = 'itemtype';
        
    if (!empty($invalid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'hooks', 'modulemodifyconfig', 'Twitter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return '';
    }

    // get settings for current module
    $data = xarModAPIFunc('twitter', 'hooks', 'getsettings', 
        array('module' => $module, 'itemtype' => $itemtype));
        
    return $data;
}
?>