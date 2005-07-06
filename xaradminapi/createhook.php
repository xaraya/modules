<?php

/**
    Create Security level for a xaraya item
*/
function security_adminapi_createhook($args)
{
    extract($args);
    
    xarModAPILoad('security', 'user');
        
    // setup vars for insertion
    $modid = '';
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = '';    
    if( !empty($extrainfo['itemtype']) )
        $itemtype = xarModGetIdFromName($extrainfo['itemtype']);
        
    $itemid = '';    
    if( !empty($objectid) )
        $itemid = $objectid;
 
    $roles = new xarRoles();
    $user = $roles->getRole( xarUserGetVar('uid') );
    $group = current($user->getParents());
    $gid = $group->uid;   
    $sargs = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid,
                  'gid' => $gid
    );
    xarModAPIFunc('security', 'admin', 'create', $sargs);
        
    return $extrainfo;    
}

?>