<?php
/**
    Security - Provides unix style privileges to xaraya items.
 
    @package Xaraya Modules
    @copyright (C) 2003-2005 by Envision Net, Inc.
    @license GPL {@link http://www.gnu.org/licenses/gpl.html}
    @link http://www.envisionnet.net/
 
    @subpackage Security module
	@link http://www.envisionnet.net/home/products/security/
    @author Brian McGilligan <brian@envisionnet.net>
*/

/**
    specifies module tables namees

    @param   none

    @return  $xartable array
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