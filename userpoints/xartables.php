<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 * 
 * Userpoints System
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * @subpackage userpoints module
 * @author Vassilis Stratigakis 
 */

/**
 * specifies module tables namees
 *
 * @author  Vassilis Stratigakis
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
function userpoints_xartables()
{ 
    // Initialise table array
    $xartable = array(); 
    // Name for userpoints database entities
    $userpoints = xarDBGetSiteTablePrefix() . '_userpoints'; 
    // Table name
    $xartable['userpoints'] = $userpoints; 
    // Return table information
    return $xartable;
} 

?>