<?php

function security_adminapi_create($args)
{
    extract($args);
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
       
    $table = $xartable['security'];
    $groupLevelTable = $xartable['security_group_levels'];
   
    // Default Sec levels
    $userLevel  = SECURITY_OVERVIEW+SECURITY_READ+SECURITY_COMMENT+SECURITY_WRITE+SECURITY_ADMIN;
    $groupLevel = SECURITY_OVERVIEW+SECURITY_READ;
    $worldLevel = SECURITY_OVERVIEW+SECURITY_READ;

    $query = "INSERT INTO $table (xar_modid, xar_itemtype, xar_itemid, xar_userlevel, xar_grouplevel, xar_worldlevel)
              VALUES ( ?, ?, ?, ?, ?, ? )
    ";
    $bindvars = array( $modid, $itemtype, $itemid, $userLevel, $groupLevel, $worldLevel );
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;    
    
    $query = "INSERT INTO $groupLevelTable (xar_modid, xar_itemtype, xar_itemid, xar_gid, xar_level)
              VALUES ( ?, ?, ?, ?, ? )
    ";
    $bindvars = array( $modid, $itemtype, $itemid, $gid, $groupLevel );
    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;
    
    return true;
}
?>