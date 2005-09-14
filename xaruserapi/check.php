<?php

/**
    Check to see if user has access to a xaraya item
    
    @param int $args['modid']
    @param int $args['itemtype']
    @param int $args['itemid']
    @param int $args['level']
    @return boolean
*/
function security_userapi_check($args)
{
    extract($args);
    
    if( xarSecurityCheck('AdminPanel', 0) ){ return true; }
    
    // Make sure the need module API's are loaded
    xarModAPILoad('owner', 'user');

    // Get current user and groups
    $currentUserId = xarUserGetVar('uid');
    $groups = array();
    
    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pre = xarDBGetSiteTablePrefix();    

    $secTable = $xartable['security'];
    $secGroupLevelTable = $xartable['security_group_levels'];
    $ownerTable = $xartable['owner'];
    
    $bindvars = array();
    $where = array();
    $query = "
        SELECT $ownerTable.xar_uid, $ownerTable.xar_gid, 
               xar_userlevel, xar_grouplevel, xar_worldlevel,
               $secGroupLevelTable.xar_gid, $secGroupLevelTable.xar_level
        FROM $secTable, $ownerTable, $secGroupLevelTable ";
    
    $where[] = " $secTable.xar_modid    = $ownerTable.xar_modid ";
    $where[] = " $secTable.xar_itemtype = $ownerTable.xar_itemtype ";
    $where[] = " $secTable.xar_itemid   = $ownerTable.xar_itemid ";
    $where[] = " $secTable.xar_modid    = $secGroupLevelTable.xar_modid ";
    $where[] = " $secTable.xar_itemtype = $secGroupLevelTable.xar_itemtype ";
    $where[] = " $secTable.xar_itemid   = $secGroupLevelTable.xar_itemid ";
    
    if( !empty($modid) )
    {
        $where[] = "$secTable.xar_modid = ?";
        $bindvars[] = $modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = "$secTable.xar_itemtype = ?";
        $bindvars[] = $itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = "$secTable.xar_itemid = ?";
        $bindvars[] = $itemid;
    }
    
    // User Check
    $secCheck[] = " ( $secTable.xar_userlevel & ? AND $ownerTable.xar_uid = ? ) ";
    $bindvars[] = $level;    
    $bindvars[] = $currentUserId;    

    //Check Groups
    $roles = new xarRoles();
    $user = $roles->getRole($currentUserId);
    $parents = $user->getParents();
    foreach( $parents as $parent )
    {
        $secCheck[] = " ( $secGroupLevelTable.xar_gid = ? AND xar_level & ? ) ";
        $bindvars[] = $parent->uid;
        $bindvars[] = $level;
    }
    
    // Check for world    
    $secCheck[] = " ( $secTable.xar_worldlevel & ? ) ";
    $bindvars[] = $level;
    
    $where[] = " ( " . join(" OR ", $secCheck) . " ) ";
    
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
     
    $result = $dbconn->Execute($query, $bindvars);
    if( $result->EOF ) return false;
    
    return true;
}
?>