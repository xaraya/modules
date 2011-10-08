<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    $xartable['ratings'] = xarDB::getPrefix() . '_ratings';
    // Return table information
    return $xartable;
}

?>