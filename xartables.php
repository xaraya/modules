<?php 
/**
 * File: $Id$
 * 
 * Ping initialization functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage ping
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
function ping_xartables()
{
    // Initialise table array
    $xartable = array();
    $ping = xarDBGetSiteTablePrefix() . '_ping';
    // Set the table name
    $xartable['ping'] = $ping;
    // Return the table information
    return $xartable;
}
?>