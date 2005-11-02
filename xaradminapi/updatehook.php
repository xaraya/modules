<?php
/**
    Creates default security for an item if none exists
    
    @param $args array standard hook params
    
    @return $extrainfo array containing standard hooks extrainfo
*/
function security_adminapi_updatehook($args)
{
    extract($args);

    xarModAPILoad('security', 'user');

    // setup vars 
    $modid = '';
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = '';    
    if( !empty($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];
        
    $itemid = '';    
    if( !empty($objectid) )
        $itemid = $objectid;
 
    // Check to see if we have an entry already
    $securityExists = xarModAPIFunc('security', 'user', 'securityexists', 
        array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid));
    
    // If this has not been owned before set ownership to current user
    if( !$securityExists )
    {   
       xarModAPIFunc('security', 'admin', 'createhook', $args);
    }
    
    return $extrainfo;    
}

?>