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

    $secTable = $xartable['security'];
    $secGroupLevelTable = $xartable['security_group_levels'];
    $ownerTable = $xartable['owner'];
    
    $bindvars = array();
    $where = array();
    $query = "
        SELECT $ownerTable.xar_uid, xar_userlevel, xar_worldlevel,
               $secGroupLevelTable.xar_gid, $secGroupLevelTable.xar_level
        FROM $secTable 
        LEFT JOIN $ownerTable ON 
            $secTable.xar_modid    = $ownerTable.xar_modid  AND
            $secTable.xar_itemtype = $ownerTable.xar_itemtype AND
            $secTable.xar_itemid   = $ownerTable.xar_itemid             
        LEFT JOIN $secGroupLevelTable ON
            $secTable.xar_modid    = $secGroupLevelTable.xar_modid AND
            $secTable.xar_itemtype = $secGroupLevelTable.xar_itemtype AND
            $secTable.xar_itemid   = $secGroupLevelTable.xar_itemid
    ";
        
    if( !empty($modid) )
    {
        $where[] = "$secTable.xar_modid = ?";
        $bindvars[] = (int)$modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = "$secTable.xar_itemtype = ?";
        $bindvars[] = (int)$itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = "$secTable.xar_itemid = ?";
        $bindvars[] = (int)$itemid;
    }
    
    // User Check
    $currentUserId = (int)$currentUserId;
    $level = (int)$level;
    $secCheck[] = " ( $secTable.xar_userlevel & $level AND $ownerTable.xar_uid = $currentUserId ) ";

    //Check Groups
    $roles = new xarRoles();
    $user = $roles->getRole($currentUserId);
    $parents = $user->getParents();
    foreach( $parents as $parent )
    {
        $secCheck[] = " ( $secGroupLevelTable.xar_gid = {$parent->uid} AND xar_level & $level ) ";
    }
    
    // Check for world    
    $secCheck[] = " ( $secTable.xar_worldlevel & $level ) ";
    
    $where[] = " ( " . join(" OR ", $secCheck) . " ) ";
    
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    $result = $dbconn->Execute($query, $bindvars);
    
    if( $result->EOF ) 
    {
        if( empty($hide_exception) )
        {
            $msg = "You do not have the proper security to perform this action!";
            xarErrorSet(XAR_USER_EXCEPTION, 'NO_PRIVILEGES', $msg);
        }
        return false;
    }
    
    return true;
}
?>