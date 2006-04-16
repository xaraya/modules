<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Owner Module
 * @author Brian McGilligan <brian@mcgilligan.us>
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