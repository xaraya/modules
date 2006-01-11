<?php

function security_adminapi_create_group_level($args)
{
    extract($args);

    xarModAPILoad('security', 'user');

    // Set the default
    //$level = SECURITY_READ;

    // Get DB conn ready
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security_group_levels'];

    $query = "INSERT INTO $table (xar_modid, xar_itemtype, xar_itemid, xar_gid, xar_level)
        VALUES ( ?, ?, ?, ?, ? )
    ";
    $bindvars = array( $modid, $itemtype, $itemid, $group, $level );

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ) return false;

    return true;
}
?>