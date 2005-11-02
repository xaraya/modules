<?php
/**
    Creates default security for an item if none exists
    
    @param $args array standard hook params
    
    @return $extrainfo array containing standard hooks extrainfo
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
 
    /*
        Check args and set any needed exceptions    
    */    
    if( empty($modid) )
    {
        $msg = "Missing module id in security_adminapi_createhook";
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MISSING_PARAM', $msg);
        return false;
    }        
        
    /*
        Get the default settings for this module / itemtype pair
    */
    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid'    => isset($modid) ? $modid : null,
            'itemtype' => isset($itemtype) ? $itemtype : null
        )
    );

    /*
        Check if there are any extra security group 
    */
    if( !xarVarFetch('security_extra_groups', 'array', $extra_groups, array(), XARVAR_NOT_REQUIRED) ){ return false; }
    foreach( $extra_groups as $group )
    {
        if( 
            empty($settings['exclude_groups'][$group]) && 
            empty($settings['levels']['groups'][$group]) &&
            $group > 2 
        )
        {
            $settings['levels']['groups'][$group] = $settings['default_group_level'];
        }    
    }

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
            $settings['levels']['groups'][$parent->uid] = $settings['default_group_level'];
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