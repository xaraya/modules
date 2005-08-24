<?php

function security_admin_changesecurity($args)
{
    extract($args);

    if( xarRequestGetVar('type') == 'admin' ) return '';
    
    // Setup xaraya item variables
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
    
    // Make sure their are levels if not quit
    $args = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid);
    $levels = xarModAPIFunc('security', 'user', 'get', $args);
    if( !$levels ) return '';

    // Make user this has an owner otherwise quit
    $owner = xarModAPIFunc('owner', 'user', 'get', $args);
    if( !$owner ) return '';

    // If owner is not current user or Admin quit
    if( $owner['uid'] != xarUserGetVar('uid') && 
        !xarSecurityCheck('AdminPanel', 0) ) return '';    
    
    // Get groups
    $groups = xarModAPIFunc('roles', 'user', 'getancestors', array('uid' => $owner['uid']));
    $groupCache = array();
    foreach ($groups as $key => $group) 
    {
    	if( isset($levels['groups'][$group['uid']]) )
    	{
    	    $groupCache[$group['uid']] = $group;
    	    unset($groups[$key]);
    	}
    	
    	if( $group['uid'] == 1 )
    	   unset($groups[$key]);
    }
           
    $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
    
    // Calc Security Levels and make a Map
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
    
    
    // Setup vars for the template
    $data['secLevels']= $secLevels; // different security levels
    $data['secMap']   = $secMap; // Security Map
    $data['levels']   = $levels; // Sec levels for each group
    $data['owner']    = $owner;
    $data['groups']   = $groups;
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