<?php
/**
 * Return example table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function opentracker_xartables()
{ 
    // Initialise table array
    $prefix = xarDBGetSiteTablePrefix();
    $xarTables = array(); 
    $xarTables['add_data'] = $prefix . '_pot_add_data'; 
    $xarTables['accesslog'] = $prefix . '_pot_accesslog'; 
    $xarTables['documents'] = $prefix . '_pot_documents'; 
    $xarTables['exit_targets'] = $prefix . '_pot_exit_targets'; 
    $xarTables['hostnames'] = $prefix . '_pot_hostnames'; 
    $xarTables['operating_systems'] = $prefix . '_pot_operating_systems'; 
    $xarTables['referers'] = $prefix . '_pot_referers'; 
    $xarTables['user_agents'] = $prefix . '_pot_user_agents'; 
    $xarTables['visitors'] = $prefix . '_pot_visitors'; 
    $xarTables['search_engines'] = $prefix . '_pot_search_engines'; 

    // Return the table information
    return $xarTables;
} 

?>