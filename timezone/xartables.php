<?php 

function timezone_xartables()
{
    // Initialise table array
    $xartable = array();
    
    // set up the events table
    $tz = xarDBGetSiteTablePrefix() . '_timezones';
    $xartable['timezones'] = $tz;
    
    return $xartable;
}

?>
