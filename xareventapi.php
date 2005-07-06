<?php

function security_eventapi_OnServerRequest($args)
{
    //if( xarSecurityCheck('AdminPanel', 0) ) return;
    
    $module = xarRequestGetVar('module');
    $catid = xarRequestGetVar('catid');
    xarModIsHooked('security', $module);
        
    if( !empty($catid) && xarModIsHooked('security', 'categories') )
    {    
        $modid = xarModGetIdFromName('categories');
        
        if( !isset($itemtype) )
            $itemtype = '';

        if( empty($itemid) )
            $itemid = $catid;
        
        // If no security exists then we don't care about it
        $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
        $securityExists = xarModAPIFunc('security', 'user', 'securityexists', $args);
        if( !$securityExists )
            return true;
            
        $table = xarModAPIFunc('security', 'user', 'leftjoin', $args);
        //var_dump($table);
        
        $args['level'] = SECURITY_READ;    
        $check = xarModAPIFunc('security', 'user', 'check', $args);
        if( !$check )
        {
            $msg = "You don't have privs";
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return false;        
        }                
    }

    return true; 
}
?>