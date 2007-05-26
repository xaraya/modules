<?php
/**
 * This function is called internally by the core whenever the module is
 * loaded.
 */
function sitesearch_xartables()
{
    // Set module prefix for tables
    $table_prefix = xarDBGetSiteTablePrefix();
    $prefix = "_sitesearch";

    // Initialise table array
    $xartable = array();

    $table = $table_prefix . $prefix . '_query_log';

    $xartable['sitesearch_query_log'] = $table;

    // Return the table information
    return $xartable;
}
?>