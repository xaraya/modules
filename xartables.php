<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
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