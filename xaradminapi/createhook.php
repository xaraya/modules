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
        $itemtype = $extrainfo['itemtype'];
        
    $itemid = '';    
    if( !empty($objectid) )
        $itemid = $objectid;
 
    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid'    => isset($modid) ? $modid : null,
            'itemtype' => isset($itemtype) ? $itemtype : null
        )
    );

    $roles = new xarRoles();
    $user = $roles->getRole( xarUserGetVar('uid') );
    $parents = $user->getParents();
    foreach( $parents as $parent )
    {
        // We also want to always exclude Everybody cause it's
        if( 
            empty($settings['exclude_groups'][$parent->uid]) && 
            empty($settings['levels']['groups'][$parent->uid]) &&
            $parent->uid > 2 
        )
        {
            // Replace this level with a configurable one
            $settings['levels']['groups'][$parent->uid] = SECURITY_OVERVIEW+SECURITY_READ;
        }
    }
        
    $sargs = array(
        'modid'    => $modid, 
        'itemtype' => $itemtype, 
        'itemid'   => $itemid,
        'settings' => $settings
    );
    xarModAPIFunc('security', 'admin', 'create', $sargs);
        
    return $extrainfo;    
}

?>