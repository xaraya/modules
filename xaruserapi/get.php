<?php
/**
    Get the security of a xaraya item
*/
function security_userapi_get($args)
{
    extract($args);
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pre = xarDBGetSiteTablePrefix();    
    $table = $xartable['security'];
    $groupLevelsTable = $xartable['security_group_levels'];
    
    $bindvars = array();
    $where = array();
    $query = "SELECT xar_userlevel, xar_grouplevel, xar_worldlevel
              FROM $table
    ";        
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
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }    
    $result = $dbconn->Execute($query, $bindvars);
    if( $result->EOF ) return array();

    list($u, $g, $w) = $result->fields;
    $level = array('user' => $u, 'group' => $g, 'world' => $w);
    
    // Now Get all the group privs
    $query = "SELECT xar_gid, xar_level
        FROM $groupLevelsTable
    ";
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }    
    $result = $dbconn->Execute($query, $bindvars);

    $level['groups'] = array();
    while( (list($gid, $l) = $result->fields) != null ) 
    {        
        $level['groups'][$gid] = $l;
        $result->MoveNext();	
    }
        
    return $level;
}
?>