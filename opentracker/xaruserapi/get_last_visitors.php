<?php
/**
 * gets the last xaraya-users for a module page (based on modulename/function/instanceid)
 * 
 * @author Chris "Alley" van de Steeg
 * @param  $args['modname'] The name of the module to get the info for
 * @param  $args['modtype'] (Optional) The type of api used for the page (eg. 'user' or 'admin')
 * @param  $args['modfunc'] (Optional) The type of function that was called
 * @param  $args['instanceid'] (Optional) The instanceid that has been viewed
 * @param  $args['include_anonymous'] (Optional) Boolean wheter or not to include the anonymous user in the resultset (default = false)
 * @param  $args['num_users'] (Optional) Integer specifying how many users to return (default = 10)
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function opentracker_userapi_get_last_visitors($args) {
	extract($args);
	
    if (!isset($modname)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
        		    'Module name', 'opentracker', 'get last visitors', 'Opentracker');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } 
    
    if (!isset($include_anonymous))
    	$include_anonymous = false;
    
    if (!isset($num_users) || !is_numeric($num_users))
    	$num_users = 10;
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables(); 
    
    $query = 	'SELECT DISTINCT(AL.xar_uid) FROM '.$xartable['accesslog'].
    			' AL, '.$xartable['roles'].' R'.
    			' WHERE R.xar_uid = AL.xar_uid AND AL.xar_modname = \''.xarVarPrepForStore($modname) .'\'';
    if (isset($modtype))
    	$query .= ' AND AL.xar_modtype = \''.xarVarPrepForStore($modtype).'\'';
    if (isset($modfunc))
    	$query .= ' AND AL.xar_modfunc = \''.xarVarPrepForStore($modfunc).'\'';
    if (isset($instanceid))
    	$query .= ' AND AL.xar_instanceid = \''.xarVarPrepForStore($instanceid).'\'';
    if (!$include_anonymous)
    	$query .= ' AND R.xar_uname <> \'anonymous\'';
    
    $query .= ' ORDER BY AL.timestamp';
    
    $result = $dbconn->SelectLimit($query, $num_users); 

    if (!$result) return; 
    $items = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($uid) = $result->fields;
        $items[] = $uid;
    }
    return $items;
        
}

?>