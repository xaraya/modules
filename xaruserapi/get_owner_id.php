<?php


function security_userapi_get_owner_id($args)
{
    extract($args);

    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid'    => $modid,
            'itemtype' => $itemtype
        )
    );

    // Make user this has an owner otherwise quit
    if( is_null($settings['owner']) )
    {
        $owner = xarModAPIFunc('owner', 'user', 'get', $args);
        if( !$owner ) return false;
    }
    else
    {
        // Use owner table field settings to extract the owner from the database
        $dbconn   =& xarDBGetConn();
        $sql = "
            SELECT {$settings['owner']['column']}
            FROM {$settings['owner']['table']}
            WHERE {$settings['owner']['primary_key']} = ?
        ";
        $result = $dbconn->Execute($sql, array($itemid));
        if( !$result ){ return false; }
        $owner['uid'] = $result->fields[0];
    }

    return $owner['uid'];
}
?>