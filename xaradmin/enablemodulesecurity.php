<?php
function security_admin_enablemodulesecurity($args)
{
    if( !xarSecurityCheck('AdminSecurity') ) return false;

    xarModAPILoad('Security');
    $default_user_level  = SECURITY_OVERVIEW+SECURITY_READ+SECURITY_COMMENT+SECURITY_WRITE+SECURITY_ADMIN;
    $default_group_level  = SECURITY_OVERVIEW+SECURITY_READ;
    $default_world_level  = SECURITY_OVERVIEW+SECURITY_READ;    
    
    xarVarFetch('mod',        'str', $module,     'categories', XARVAR_NOT_REQUIRED);
    xarVarFetch('table',      'str', $table,      $module, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',   'int', $itemtype,   0,    XARVAR_NOT_REQUIRED);
    
    xarVarFetch('user_level', 'int', $user_level, $default_user_level, XARVAR_NOT_REQUIRED);
    xarVarFetch('group_level','int', $group_level,$default_group_level, XARVAR_NOT_REQUIRED);
    xarVarFetch('world_level','int', $world_level,$default_world_level, XARVAR_NOT_REQUIRED);
    xarVarFetch('uid',        'str', $uid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED);
    xarVarFetch('group',      'int', $gid,         null, XARVAR_NOT_REQUIRED);
    xarVarFetch('submit',     'str', $submit,      null, XARVAR_NOT_REQUIRED);
    
    /*
        Setup a Datadict object 
    */
    $dbconn =& xarDBGetConn();
    $dict   =& xarDBNewDataDict($dbconn);

    if( $submit )
    {
        $modid = xarModGetIdFromName($module);

        xarModLoad('owner', 'user');

        /*
            Get and examine columns to try and find the primary key or id for the item
        */
        $cols = $dict->getColumns($table);
        foreach( $cols as $col )
        {
            if( $col->primary_key && $col->type == 'int' )
            {
                $itemIdCol = $col->name;
                break;
            }
        }
        //var_dump($cols);
        if( !isset($itemIdCol) )
        {
            $msg = 'Error! Could not find the primary key';
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', $msg);
            return false;
        }    

        var_dump($uid);
        // Now get all ids from DB
        $query = "SELECT $itemIdCol, $uid FROM $table ";
        $rows = $dbconn->Execute($query);   
            
        while( (list($itemid, $uid) = $rows->fields) != null )
        {
            $secArgs = array(
                'modid'      => $modid, 
                'itemtype'   => $itemtype, 
                'itemid'     => $itemid,
                'uid'        => $uid,
                'userLevel'  => $user_level,
                'groupLevel' => $group_level,
                'worldLevel' => $world_level,
                'gid'        => $gid 
            );
            var_dump($uid);
            $exists = xarModAPIFunc('security', 'user', 'securityexists', $secArgs);
            if( !$exists )
                xarModAPIFunc('security', 'admin', 'create', $secArgs);

            $exists = xarModAPIFunc('owner', 'user', 'ownerexists', $secArgs);
            if( !$exists )
                xarModAPIFunc('owner', 'admin', 'create', $secArgs);
            
            $rows->MoveNext();
        }    
    }
    
    extract($args);
    
    $data = array();   
    
    $data['modules'] = xarModAPIFunc('modules', 'admin', 'getdbmodules');
    $data['tables']  = $dict->getTables();
    $data['groups'] = xarModAPIFunc('roles', 'user', 'getallgroups');

    $data['user_level']  = $user_level;
    $data['group_level'] = $group_level;
    $data['world_level'] = $world_level;
    
    return $data;
}
?>