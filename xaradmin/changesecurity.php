<?php

function security_admin_changesecurity($args)
{
    extract($args);
    if( xarRequestGetVar('type') == 'admin' || xarRequestGetVar('func') == 'modify' ) return '';
    
    /*
        Process the std. hook info
    */
    if( !empty($extrainfo['module']) )
        $modid = xarModGetIdFromName($extrainfo['module']);

    $itemtype = '';
    if( !empty($extrainfo['itemtype']) )
        $itemtype = $extrainfo['itemtype'];
        
    $itemid = '';
    if( !empty($objectid) )
        $itemid = $objectid;

    $returnUrl = '';
    if( !empty($extrainfo['returnurl']) )
        $returnUrl = $extrainfo['returnurl'];
    else 
        $returnUrl = xarServerGetCurrentURL();
        
    $data = array();    
    
    /*
        Get all the current security and owner info
    */
    // Make sure their are levels if not quit
    $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
    $levels = xarModAPIFunc('security', 'user', 'get', $args);
    if( !$levels ) return '';

    // Make user this has an owner otherwise quit
    $owner = xarModAPIFunc('owner', 'user', 'get', $args);
    if( !$owner ) return '';

    /*
        If owner is not current user or Admin quit
    */
    if( $owner['uid'] != xarUserGetVar('uid') && 
        !xarSecurityCheck('AdminPanel', 0) ) return '';    
    
    /*
        Get all the groups just incase it's needed for display purposes
    */
    $all_groups = xarModAPIFunc('roles', 'user', 'getallgroups');
    $groupCache = array();
    foreach( $all_groups as $key => $group )
    {
        $groupCache[$group['uid']] = $group;
    }
    
    /*
        If an admin allow admin to change privs as if they were the owner.
        This allows the admin to assign privs how ever they want even if the
        user can not do it.
    */
    if( xarSecurityCheck('AdminPanel', 0) )
        $uid = xarUserGetVar('uid');
    else    
        $uid = $owner['uid'];
        
    /*
        These groups are used in the Add groups menu thing to create new group privs
    */
    $groups = xarModAPIFunc('roles', 'user', 'getancestors', array('uid' => $uid));
    $tmp = array();
    foreach ($groups as $key => $group) 
    {
        $tmp[$group['uid']] = $group;    
    }
    $groups = $tmp;

    $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
    
    /*
        Calc Security Levels and make a Map
    */
    $secMap = array();
    foreach( $secLevels as $secLevel )
    {
        $currentLevel = $secLevel['level'];
        $tmp = $levels['user'] & $secLevel['level'];
        $secMap['user'][$currentLevel] = $levels['user'] & $currentLevel;
        $secMap['world'][$currentLevel] = $levels['world'] & $currentLevel;
        foreach( $levels['groups'] as $gid => $group )
        {
            $secMap[$gid][$currentLevel] = $group & $currentLevel;
        }
    }
    
    /*
        Setup vars for the template
    */
    $data['secLevels']= $secLevels; // different security levels
    $data['secMap']   = $secMap; // Security Map
    $data['levels']   = $levels; // Sec levels for each group
    $data['owner']    = $owner;
    $data['all_groups'] = $all_groups;
    $data['user_groups']   = $groups; // Groups user is in
    $data['groupCache']= $groupCache;
    $data['showRemove']= count($groupCache) > 1 ? true : false;
    $data['modid']    = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid']   = $itemid;
    $data['action']   = xarModURL('security', 'admin', 'creategroupsecurity');
    $data['returnurl']= $returnUrl;
    
    return $data;
}
?>