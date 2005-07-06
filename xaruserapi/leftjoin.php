<?php

/**
    Provide SQL info to do a join on the security table
    
    @param $args['module']
    @param $args['modid']
    @param $args['itemtype']
    @param $args['itemid']
    @param $args['iids']
    @param $args['level']
    
    @return array
*/
function security_userapi_leftjoin($args)
{
    extract($args);
    
    $info = array();

    xarModAPILoad('owner', 'user');
    
    if( !isset($level) )
        $level = SECURITY_OVERVIEW;
    
    // Get current user and groups
    $currentUserId = xarUserGetVar('uid');
    $groups = array();
    
    $xartable =& xarDBGetTables();
       
    $info['table'] = $xartable['security'] . ', ' . 
                     $xartable['security_group_levels'] . ', ' .
                     $xartable['owner'] . ' ';

    $secTable = $xartable['security'];
    $secGroupLevelTable = $xartable['security_group_levels'];
    $ownerTable = $xartable['owner'];

    $where[] = " $secTable.xar_modid    = $ownerTable.xar_modid ";
    $where[] = " $secTable.xar_itemtype = $ownerTable.xar_itemtype ";
    $where[] = " $secTable.xar_itemid   = $ownerTable.xar_itemid ";
    $where[] = " $secTable.xar_modid    = $secGroupLevelTable.xar_modid ";
    $where[] = " $secTable.xar_itemtype = $secGroupLevelTable.xar_itemtype ";
    $where[] = " $secTable.xar_itemid   = $secGroupLevelTable.xar_itemid ";
    
    if( !empty($modid) )
    {
        $where[] = " $secTable.xar_modid = $modid ";
    }
    if( !empty($itemtype) )
    {
        $where[] = " $secTable.xar_itemtype = $itemtype ";
    }
    
    if( !empty($iids) )
    {
        if( is_string($iids) )
            $where[] = "$secTable.xar_itemid = $iids";
        else if( is_array($iids) )
            $where[] = "$secTable.xar_itemid IN ( " . join(', ', $iids) . " )";
        
    }
    else if( !empty($itemid) )
    {
        $where[] = "$secTable.xar_itemid = $itemid";
    }
    
   // User Check
    $secCheck[] = " ( $secTable.xar_userlevel & $level AND $ownerTable.xar_uid = $currentUserId ) ";

    //Check Groups
    $roles = new xarRoles();
    $user = $roles->getRole($currentUserId);
    $parents = $user->getParents();
    foreach( $parents as $parent )
        $secCheck[] = 
            " ( $secGroupLevelTable.xar_gid = $parent->uid AND xar_level & $level ) ";
    
    // Check for world    
    $secCheck[] = " ( $secTable.xar_worldlevel & $level ) ";
    
    $where[] = " ( " . join(" OR ", $secCheck) . " ) ";
    
    if( count($where) > 0 )
    {
        $info['where'] = ' ( ' . join(' AND ', $where) . ' ) ';
    }
    
    return $info;
}
?>