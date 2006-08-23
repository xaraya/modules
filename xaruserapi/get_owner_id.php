<?php


function security_userapi_get_owner_id($args)
{
    extract($args);

    $settings = SecuritySettings::factory($modid, $itemtype);

    // Make user this has an owner otherwise quit
    if( is_null($settings->owner_table) )
    {
        $owner = xarModAPIFunc('owner', 'user', 'get', $args);
        if( !$owner ) return false;
    }
    else
    {
        // Use owner table field settings to extract the owner from the database
        $dbconn   =& xarDBGetConn();
        $sql = "
            SELECT {$settings->owner_column}
            FROM {$settings->owner_table}
            WHERE {$settings->owner_primary_key} = ?
        ";
        $result = $dbconn->Execute($sql, array($itemid));
        if( !$result ){ return false; }
        $owner['uid'] = $result->fields[0];
    }

    return $owner['uid'];
}
?>