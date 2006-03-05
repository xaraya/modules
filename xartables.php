<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package Xaraya Modules
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.envisionnet.net/
 *
 * @subpackage Owner module
 * @link http://www.envisionnet.net/home/products/security/
 * @author Brian McGilligan <brian@envisionnet.net>
 */
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
function owner_xartables()
{
    // Initialise table array
    $xartable = array();
    // Name for ratings database entities
    $table = xarDBGetSiteTablePrefix() . '_owner';
    // Table name
    $xartable['owner'] = $table;
    // Return table information
    return $xartable;
}

?>