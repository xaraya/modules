<?php

function security_eventapi_OnServerRequest($args)
{
    //if( xarSecurityCheck('AdminPanel', 0) ) return;

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
            
        $table = xarModAPIFunc('security', 'user', 'leftjoin', $args);
        
        $args['level'] = SECURITY_READ;    
        $check = xarModAPIFunc('security', 'user', 'check', $args);
        if( !$check )
        {
            $msg = "You don't have the proper security level to view this Item.";
            xarErrorSet(XAR_USER_EXCEPTION, 'NO_SECURITY', $msg);
            return false;        
        }                
    }

    return true; 
}
?>