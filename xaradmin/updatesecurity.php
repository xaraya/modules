<?php
/**
    Updates the security of a module item
    
    @param     
    
    @return boolean returns false to stop processing for the redirect
*/
function security_admin_updatesecurity($args)
{
    extract($args);
    
    xarVarFetch('modid',    'id', $modid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemid',   'id', $itemid, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('returnurl','str',$returnUrl, '', XARVAR_NOT_REQUIRED);

    xarVarFetch('user',   'array', $user,   array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('groups', 'array', $groups, array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('world',  'array', $world,  array(), XARVAR_NOT_REQUIRED);
       
    // Calc all new levels
    $userLevel = 0;
    foreach( $user as $part )
        $userLevel += $part;
       
    $groupsLevel = array();
    foreach( $groups as $key => $group )
    {
        $groupsLevel[$key] = 0;
        foreach( $group as $part )
            $groupsLevel[$key] += $part;
    }
    
    $worldLevel = 0;
    foreach( $world as $part )
        $worldLevel += $part;
        
    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['security'];

    $query = "UPDATE $table
        SET xar_userlevel = ?,
            xar_worldlevel = ?
    ";
    $bindvars = array($userLevel, $worldLevel);
    $where = array();
    if( !empty($modid) )
    {
        $where[] = " xar_modid = ? ";
        $bindvars[] = $modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = " xar_itemtype = ? ";
        $bindvars[] = $itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = " xar_itemid = ? ";
        $bindvars[] = $itemid;
    }
    
    if( count($where) > 0 )
        $query .= ' WHERE ' . join(" AND ", $where);
    
    $dbconn->Execute($query, $bindvars);    
        
    // Update group levels
    $table = $xartable['security_group_levels'];  

    // Zero out security for group
    if( empty($groupsLevel) )
        $groupsLevel[0] = 0;       
    foreach( $groupsLevel as $gid => $groupLevel )
    {
        $query = "UPDATE $table
            SET xar_level = ?
        ";
        $bindvars = array($groupLevel);
        $where = array();
        if( !empty($modid) )
        {
            $where[] = " xar_modid = ? ";
            $bindvars[] = $modid;
        }
        if( !empty($itemtype) )
        {
            $where[] = " xar_itemtype = ? ";
            $bindvars[] = $itemtype;
        }
        if( !empty($itemid) )
        {
            $where[] = " xar_itemid = ? ";
            $bindvars[] = $itemid;
        }
        if( !empty($gid) )
        {
            $where[] = " xar_gid = ? ";
            $bindvars[] = $gid;
        }
        
        if( count($where) > 0 )
            $query .= ' WHERE ' . join(" AND ", $where);
        
        $dbconn->Execute($query, $bindvars);    
    }
    
    xarResponseRedirect($returnUrl);
    
    return false;
}
?>