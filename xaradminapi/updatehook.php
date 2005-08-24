<?php

/**
    Logs who created a xaraya item
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
        $roles = new xarRoles();
        $user = $roles->getRole( xarUserGetVar('uid') );
        $group = current($user->getParents());
        $gid = $group->uid;   
        $sargs = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid,
                      'gid' => $gid
        );
        xarModAPIFunc('security', 'admin', 'create', $sargs);
    }
    
    return $extrainfo;    
}

?>