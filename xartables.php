<?php
function netquery_xartables()
{
    $xarTables = array();
    $basename = 'netquery';
    foreach(array('whois', 'lgrouter', 'geocc', 'geoip', 'flags', 'ports') as $table) {
        $xarTables[$basename . '_' . $table] = xarDBGetSiteTablePrefix() . '_' . $basename . '_' . $table;
    }
    return $xarTables;
} 
?>