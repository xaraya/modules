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
    $pointstypes = xarDBGetSiteTablePrefix() . '_userptypes';
    // Table name
    $xartable['userpoints'] = $userpoints; 
    $xartable['pointstypes'] = $pointstypes;
    // Return table information
    return $xartable;
} 

?>