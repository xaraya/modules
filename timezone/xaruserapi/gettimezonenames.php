<?php

// returns an array of timezone names
function &timezone_userapi_getTimezoneNames()
{
    // set this up so we only process this once in case there are multiple calls to this function
    static $timezones;
    if(isset($timezones) && is_array($timezones)) {
        return $timezones;
    }
    
    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();
    
    $timezones = array();
    
    $zones =& $xartables['timezone_zones'];
    $links =& $xartables['timezone_links'];
    
    $sql_zones = "SELECT id, name FROM $zones";
    $sql_links = "SELECT id, name FROM $links";
            
    $result_zones =& $dbconn->Execute($sql_zones);
    
    for($i=count($timezones); $result_zones, !$result_zones->EOF; $result_zones->MoveNext(), $i++) {
        $timezones[$i]['id'] = $result_zones->fields[0];
        $timezones[$i]['name'] = $result_zones->fields[1];
        $timezones[$i]['alias'] = false;
    }
    $result_zones->Close();  
    
    $result_links =& $dbconn->Execute($sql_links);
    
    for($i=count($timezones); $result_links, !$result_links->EOF; $result_links->MoveNext(), $i++) {
        $timezones[$i]['id'] = 'a'.$result_links->fields[0];
        $timezones[$i]['name'] = $result_links->fields[1];
        $timezones[$i]['alias'] = true;
    }
    $result_links->Close();

    // sort the timezones by name
    usort($timezones,'sort_timezones');
    return $timezones;    
}

function sort_timezones($a,$b) 
{
    if($a['name'] > $b['name']) return 1;
    elseif($a['name'] < $b['name']) return -1;
    else return 0;
    
}
?>