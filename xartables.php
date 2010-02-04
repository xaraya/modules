<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * specifies module tables namees
 *
 * @author  Andrea Moro
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
function sharecontent_xartables()
{
    // Initialise table array
    $xartable = array();
    // Name for hitcount database entities
    $sharecontent = xarDB::getPrefix() . '_sharecontent';
    // Table name
    $xartable['sharecontent'] = $sharecontent;
    // Return table information
    return $xartable;
}

?>
