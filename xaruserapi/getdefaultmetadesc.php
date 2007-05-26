<?php

function sitesearch_userapi_getdefaultmetadesc($args)
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $blockInstancesTable      = $tables['block_instances'];
    
    // Fetch details of all blocks in the group.
    $sql = "
        SELECT xar_content
            FROM $blockInstancesTable
            WHERE xar_name IN ('metadata', 'meta') 
    ";
    $result = $dbconn->Execute($sql);
    
    if( !$result ) return '';
    
    $content = unserialize($result->fields[0]);
    
    return $content['metadescription'];
}
?>