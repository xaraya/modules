<?php
function twitter_hooks_modulemodifyconfig($args)
{
    extract($args);

    if (empty($extrainfo)) $extrainfo = array();

    if (!xarSecurityCheck('AdminTwitter', 0)) return '';

    if (!is_array($extrainfo))
        $invalid[] = 'extrainfo';    
    
    if (empty($module)) {
        if (isset($extrainfo['module'])) {
            $module = $extrainfo['module'];
        } elseif (!empty($objectid) && is_string($objectid)) {
            $module = $objectid;
        } else {
            list($module) = xarController::$request->getInfo();
        }
    }
    $module_id = xarMod::getRegID($module);
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
    $data = xarMod::apiFunc('twitter', 'hooks', 'getsettings', 
        array('module' => $module, 'itemtype' => $itemtype));
        
    // @todo: fieldoptions, stateoptions
   
    $data['module'] = $module;
    $data['itemtype'] = $itemtype;
    //print_r($data);  
    return $data;
}
?>