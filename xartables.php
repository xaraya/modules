<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * Ratings System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage ratings module
 * @author Jim McDonald 
 */

/**
 * specifies module tables namees
 *
 * @author  Jim McDonald
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
function ratings_xartables()
{ 
    // Initialise table array
    $xartable = array(); 
    // Name for ratings database entities
    $ratings = xarDBGetSiteTablePrefix() . '_ratings'; 
    // Table name
    $xartable['ratings'] = $ratings; 
    // Return table information
    return $xartable;
} 

?>