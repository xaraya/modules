<?php 
function & timezone_xartables()
{
    // Initialise table array
    static $xartable;
    if(!isset($xartable)) {
        $xartable = array();
        $prefix = xarDBGetSiteTablePrefix();
        // let us define our table structure
        $xartable['timezone_zones']                 = $prefix . '_timezone_zones';
        $xartable['timezone_zones_data']            = $prefix . '_timezone_zones_data';
        $xartable['timezone_links']                 = $prefix . '_timezone_links';
        $xartable['timezone_rules']                 = $prefix . '_timezone_rules';
        $xartable['timezone_rules_data']            = $prefix . '_timezone_rules_data';
        $xartable['timezone_zones_data_has_rules']  = $prefix . '_timezone_zones_data_has_rules';
        $xartable['timezone_zones_has_links']        = $prefix . '_timezone_zones_has_links';
    }
    // return the structure
    return $xartable;
}
?>