<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
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
    $ratings = xarDBGetSiteTablePrefix() . '_ratings';
    // Table name
    $xartable['ratings'] = $ratings;
    // Return table information
    return $xartable;
}

?>