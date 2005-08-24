<?php
function security_admin_enablemodulesecurity($args)
{
    if( !xarSecurityCheck('AdminSecurity') ) return false;
    
    xarVarFetch('mod',      'str', $module,   'categories', XARVAR_NOT_REQUIRED);
    xarVarFetch('table',    'str', $table,   $module, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype', 'int', $itemtype, 0,    XARVAR_NOT_REQUIRED);
    xarVarFetch('uud',      'str', $uid,      null, XARVAR_NOT_REQUIRED);
    xarVarFetch('submit',   'str', $submit,   null, XARVAR_NOT_REQUIRED);
    
    if( $submit )
    {
        $modid = xarModGetIdFromName($module);
        $gid = 1; // Default for everybody
    
        xarModLoad($module, 'user');
        xarModLoad('owner', 'user');

        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $dict =& xarDBNewDataDict($dbconn);
        $pre = xarDBGetSiteTablePrefix();
        
        $table = $xartable[$table];
        
        $cols = $dict->getColumns($table);
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
            
            
        if( empty($uid) )    
            $uid = xarUserGetVar('uid');    
            
        // Now get all ids from DB
        $query = "SELECT $itemIdCol, $uid FROM $table ";
        $rows = $dbconn->Execute($query);   
            
        while( (list($itemid, $uid) = $rows->fields) != null )
        {
            $secArgs = array('modid' => $modid, 'itemtype' => $itemtype, 'itemid' => $itemid,
                'gid' => $gid, 'uid' => $uid
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
    }
    
    extract($args);
    
    $data = array();   
    
    return $data;
}
?>