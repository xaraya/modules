<?php

/**
 * specifies module tables namees
 *
 * @author  Brian McGilligan
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
function security_xartables()
{ 
    // Initialise table array
    $xartable = array(); 
    // Name for ratings database entities
    $table = xarDBGetSiteTablePrefix() . '_security'; 
    // Table name
    $xartable['security'] = $table; 
    $xartable['security_group_levels'] = $table . '_group_levels'; 
    // Return table information
    return $xartable;
} 

?>