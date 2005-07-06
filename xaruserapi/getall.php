<?php
/**
    Get the security of a xaraya item
*/
function security_userapi_getall($args)
{
    extract($args);
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pre = xarDBGetSiteTablePrefix();    
    $table = $xartable['security'];
    
    $bindvars = array();
    $where = array();
    $query = "SELECT xar_userlevel, xar_grouplevel, xar_worldlevel 
              FROM $table
    ";
    
    if( !empty($modid) )
    {
        $where[] = ' xar_modid = ? ';
        $bindvars[] = $modid;
    }
    if( !empty($itemtype) )
    {
        $where[] = ' xar_itemtype = ? ';
        $bindvars[] = $itemtype;
    }
    if( !empty($itemid) )
    {
        $where[] = ' xar_itemid = ? ';
        $bindvars[] = $itemid;
    }
    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }    
    $result = $dbconn->Execute($query, $bindvars);
    
    if( $result->EOF ) return array();
    
    $levels = array();
    while( (list($u, $g, $w) = $result->fields) != null )
    {
        $levels = array('user' => $u, 'group' => $g, 'world' => $w);
    }
    
    return $levels;
}
?>