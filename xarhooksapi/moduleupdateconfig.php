<?php
function twitter_hooksapi_moduleupdateconfig($args)
{
    extract($args);

    if (empty($extrainfo)) $extrainfo = array();
    
    if (!xarSecurityCheck('AdminTwitter', 0)) return $extrainfo;

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
            $itemtype = 0;
        }
    }
    if (!empty($itemtype) && !is_numeric($itemtype))
        $invalid[] = 'itemtype';
        
    if (!empty($invalid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'hooksapi', 'moduleupdateconfig', 'Twitter');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return $extrainfo;
    }

    if (!xarVarFetch('twitter_tweetcreated', 'checkbox', 
        $tweetcreated, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_textcreated', 'pre:trim:str:1:140', 
        $textcreated, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_tweetupdated', 'checkbox', 
        $tweetupdated, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_textupdated', 'pre:trim:str:1:140', 
        $textupdated, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_field', 'pre:trim:str:1:', 
        $field, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_states', 'array', 
        $states, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_includelink', 'checkbox', 
        $includelink, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_typeparam', 'pre:trim:lower:str:1:',
        $typeparam, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_funcparam', 'pre:trim:lower:str:1:',
        $funcparam, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_itypeparam', 'pre:trim:str:1:',
        $itypeparam, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('twitter_itemparam', 'pre:trim:str:1:',
        $itemparam, '', XARVAR_NOT_REQUIRED)) return;
    
    $settings = xarMod::apiFunc('twitter', 'hooks', 'getsettings', 
        array('module' => $module, 'itemtype' => $itemtype));
    
    foreach ($settings as $key => $val) {
        if (!isset($$key)) continue;
        $settings[$key] = $$key;
    }
    
    $modvar = "hooks_{$module}";
    $configs = @unserialize(xarModVars::get('twitter', $modvar));
    if (empty($configs) || !is_array($configs)) $configs = array();
    $configs[$itemtype] = $settings;      
    xarModVars::set('twitter', $modvar, serialize($configs));       
    
    $extrainfo['twitter_updateconfig'] = true;    
    
    return $extrainfo;
}
?>