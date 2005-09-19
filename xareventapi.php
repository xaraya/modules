<?php
/**
    If a xaraya item is detected perform a security check out it.
    
    @param $module   name of module
    @param $itemtype item type (optional)
    @param $itemid   item id of item
    
    @return boolean true if user has access otherwise false
    
    @throws exception when user is denied access
*/
function security_eventapi_OnServerRequest($args)
{
    if( xarSecurityCheck('AdminPanel', 0) ) return;

    $module = xarRequestGetVar('module');
    $itemtype = xarRequestGetVar('itemtype');
    $itemid = xarRequestGetVar('itemid');
    $catid = xarRequestGetVar('catid');
    
    if( empty($module) )
        $module = xarModGetName();
    
    $modid = xarModGetIdFromName($module);
    
    if( !empty($catid) && empty($itemid) && xarModIsHooked('security', 'categories') )
    {
        $modid = xarModGetIdFromName('categories');
        $module = 'categories';
        
        if( empty($itemid) )
            $itemid = $catid;    
    }

    if( !empty($itemid) && xarModIsHooked('security', $module) )
    {            
        if( empty($itemtype) )
            $itemtype = null;

        // If no security exists then we don't care about it
        $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
        $securityExists = xarModAPIFunc('security', 'user', 'securityexists', $args);
        if( !$securityExists )
            return true;
            
        $args['level'] = SECURITY_READ;    
        $check = xarModAPIFunc('security', 'user', 'check', $args);
        if( !$check ){ return false; }                
    }

    return true; 
}
?>