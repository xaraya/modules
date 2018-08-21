<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
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
    $xartable['ratings_likes'] = xarDB::getPrefix() . '_ratings_likes';
    // Return table information
    return $xartable;
}

?>