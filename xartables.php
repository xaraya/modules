<?php
/**
    Security - Provides unix style privileges to xaraya items.
 
    @copyright (C) 2003-2005 by Envision Net, Inc.
    @license GPL (http://www.gnu.org/licenses/gpl.html)
    @link http://www.envisionnet.net/
    @author Brian McGilligan <brian@envisionnet.net>
 
    @package Xaraya eXtensible Management System
    @subpackage Security module
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