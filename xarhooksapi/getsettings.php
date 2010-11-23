<?php
function twitter_hooksapi_getsettings($args)
{
    extract($args);
    if (empty($module))
        $module = xarMod::getName();
    if (empty($itemtype))
        $itemtype = 0;
        
    $defaults = @unserialize(xarModVars::get('twitter', 'hooks_twitter'));
    if (empty($defaults) || !isset($defaults[0])) {
        $defaults = array();
        $defaults[0] = array(
            'includelink' => true,
            'typeparam' => 'user',
            'funcparam' => 'display',
            'itypeparam' => 'itemtype',
            'itemparam' => 'itemid',
            'tweetcreated' => true,
            'tweetupdated' => false,
            'textcreated' => '',
            'textupdated' => '',
            'field' => '',
            'states' => array(),      
        );
    }
    
    $modvar = "hooks_{$module}";
    $configs = @unserialize(xarModVars::get('twitter', $modvar));
      
    if (!is_array($configs) || empty($configs))
        $configs = $defaults;    
    
    if (!isset($configs[0]))
        $configs = array_unshift($configs, $defaults[0]);
    
    if (!empty($itemtype) && isset($configs[$itemtype])) 
        $settings = $configs[$itemtype];

    if (empty($settings))
        $settings = $configs[0];    
    
    $settings['hasitemlinks'] = file_exists(sys::code() . "modules/{$module}/xaruserapi/getitemlinks.php");
   
    // see if module supplies an array of valid fieldnames/labels for items of this module (+itemtype) 
    try {
        // attempt to get field settings 
        $itemfields = xarModAPIFunc($module, 'user', 'getitemfields', 
            array('itemtype' => $itemtype));         
    } catch (Exception $e) {
        $itemfields = array();
    }
    $settings['itemfields'] = $itemfields;

    // check for module supplied metadata file
    $file = sys::code() . "modules/{$module}/xardata/meta.xml";
    if (!file_exists($file)) {
        // check for metadata file supplied by twitter module
        $file = sys::code() . "modules/twitter/xardata/{$module}-meta.xml";    
    } 
    if (file_exists($file)) {
        sys::import("modules.twitter.class.twitterapi");
        $meta = TwitterUtil::getMeta($file);
    }  

    // see if items of this module (+itemtype) have state transitions (eg, articles, submitted, approved, etc)
    $itemstates = array();    
    if (isset($meta['functions']['getitemstates'])) {
        $type = isset($meta['functions']['getitemstates']['type']) ? 
                $meta['functions']['getitemstates']['type'] : 'user';
        $func = isset($meta['functions']['getitemstates']['func']) ? 
                $meta['functions']['getitemstates']['func'] : 'getitemstates';
        $stateparam = isset($meta['functions']['getitemstates']['param']) ? 
                $meta['functions']['getitemstates']['param'] : 'status';
        // attempt to get states
        try {
            $itemstates = xarMod::apiFunc($module, $type, $func, array());
            $settings['stateparam'] = $stateparam;
        } catch (Exception $e) {
            $itemstates = array();
            $settings['stateparam'] = '';
        }        
    }
    $settings['itemstates'] = $itemstates;
        
    return $settings;  
}
?>