<?php 
/**
 * File: $Id$
 *
 * Headlines
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage headlines module
 * @author John Cox
*/

/**
 *
 * @author  John Cox 
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
function headlines_xartables()
{
    // Initialise table array
    $xartable = array();
    // Get the name for the example item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $headlines = xarDBGetSiteTablePrefix() . '_headlines';
    // Set the table name
    $xartable['headlines'] = $headlines;
    // Return the table information
    return $xartable;
}
?>