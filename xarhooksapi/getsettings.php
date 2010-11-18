<?php
function twitter_hooksapi_getsettings($args)
{
    extract($args);
    if (empty($module))
        $module = xarModGetName();
    if (empty($itemtype))
        $itemtype = 0;
    
    $modvar = "hooks_{$module}";
    
    if (!empty($itemtype))
        $string = xarModGetVar('twitter', "{$modvar}_{$itemtype}");
    
    if (empty($string) && !empty($itemtype))
        $string = xarModGetVar('twitter', "$modvar");
    
    if (empty($string))
        $string = xarModGetVar('twitter', 'hooks_twitter');
        
    if (!empty($string))
        $settings = @unserialize($string);
        
    if (empty($settings) || !is_array($settings))
        $settings = array(
            'typeparam' => 'user',
            'funcparam' => 'display',
            'itypeparam' => 'itemtype',
            'itemparam' => 'itemid',
            'pre' => '',
            'field' => '',
            'states' => array(),       
        );
    
    $settings['hasitemlinks'] = file_exists("modules/{$module}/xaruserapi/getitemlinks.php");

    // check for module supplied metadata file
    $file = "modules/{$module}/xardata/meta.xml";
    if (!file_exists($file)) {
        // check for metadata file supplied by twitter module
        $file = "modules/twitter/xardata/{$module}-meta.xml";    
    }    

    if (file_exists($file)) {
        require_once("modules/twitter/class/twitterapi.php");
        $meta = TwitterUtil::getMeta($file);
    }  

    // see if items of this module (+itemtype) have state transitions (eg, articles, submitted, approved, etc)
    $itemstates = array();    
    if (isset($meta['functions']['getitemstates'])) {
        $type = isset($meta['functions']['getitemstates']['type']) ? 
                $meta['functions']['getitemstates']['type'] : 'user';
        $func = isset($meta['functions']['getitemstates']['func']) ? 
                $meta['functions']['getitemstates']['func'] : 'getitemstates';
        // attempt to get states 
        $itemstates = xarModAPIFunc($module, $type, $func, array(), false);         
    }
    $settings['itemstates'] = $itemstates;
    if (empty($itemstates) || !isset($settings['states']))
        $settings['states'] = array();
        
    return $settings;  
}
?>