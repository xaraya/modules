<?php

function security_admin_fixmodule($args)
{
    extract($args);

    xarVarFetch('mod', 'str', $module, 'categories');
        
    $itemtype = 0;
    $modid = xarModGetIdFromName($module);
    $gid = 1; // Default for everybody

    xarModLoad($module, 'user');
    xarModLoad('owner', 'user');
    
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $dict =& xarDBNewDataDict($dbconn);
    $pre = xarDBGetSiteTablePrefix();
    
    $fields = array('xar_modid', 'xar_itemtype', 'xar_itemid');
    //$dict->createIndex('xar_owner_index', $xartable['owner'], $fields);
    //$dict->createIndex('xar_security_index', $xartable['security'], $fields);
    //$dict->createIndex('xar_security_group_levels_index', $xartable['security_group_levels'], $fields);
    //$index = $dict->getIndexes($xartable['security']);
    //var_dump($index);
    //return '';
    
    /*
    $tables = $dict->getTables();
    foreach( $tables as $table )
    {
        $result = $dbconn->Execute("OPTIMIZE TABLE " . $table);
        //var_dump($result->fields);
    }*/
        
    $table = $xartable[$module];
    
    $cols = $dict->getColumns($table);
    //var_dump($cols);
    
    $itemIdCol = '';
    foreach( $cols as $col )
    {
        if( $col->primary_key && $col->type == 'int' )
        {
            $itemIdCol = $col->name;
        }
    }
    
    if( !empty($itemIdCol) )
        echo "we found the item id!";
    else 
        return; // return cause this module won't work
        
    // Now get all ids from DB
    $query = "SELECT $itemIdCol FROM $table ";
    $rows = $dbconn->Execute($query);   
        
    while( (list($itemid) = $rows->fields) != null )
    {
        $secArgs = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid,
            'gid' => $gid
        );
        
        $exists = xarModAPIFunc('security', 'user', 'securityexists', $secArgs);
        if( !$exists )
            xarModAPIFunc('security', 'admin', 'create', $secArgs);

        $secArgs['uid'] = xarUserGetVar('uid');
        $exists = xarModAPIFunc('owner', 'user', 'ownerexists', $secArgs);
        if( !$exists )
            xarModAPIFunc('owner', 'admin', 'create', $secArgs);
        
        $rows->MoveNext();
    }
    
    return;
}
?>