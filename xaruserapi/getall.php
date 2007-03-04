<?php

/**
 * get overview of all calendars
 * Note : the following parameters are all optional
 *
 * @param $args['numitems'] number of articles to get
 * @param $args['startnum'] starting article number
 * @returns array
 * @return array of calendars, or false on failure
 */


function calendar_userapi_getall($args) 
{
    extract($args);
    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }

    $calendars = array();
    
    // Security check
//    if (!xarSecurityCheck('ViewCalendars')) return;
    
    $dbconn =& xarDBGetConn();  
    $xartable =& xarDBGetTables();
    $caltable = $xartable['calendars'];
    $cal_filestable = $xartable['calendars_files'];    
    $filestable = $xartable['calfiles'];    
    
    // TODO: cleanup query? --amoro
    $query = " SELECT DISTINCT $caltable.xar_id,
                               $caltable.xar_name,
                               $filestable.xar_path        
                FROM $caltable  
                LEFT JOIN $cal_filestable 
                    ON $caltable.xar_id = $cal_filestable.xar_calendars_id 
                LEFT JOIN $filestable 
                    ON $cal_filestable.xar_files_id = $filestable.xar_id ";

    // Run the query 
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cid,
             $cname,
             $cpath) = $result->fields;
        $calendars[] = array(  'cid' => $cid
                       ,'cname' => $cname
                       ,'cpath' => $cpath
                     );
    }
    $result->Close();             
    return $calendars;             
}
?>
