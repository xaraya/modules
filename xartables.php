<?php
/**
 * webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
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
function webshare_xartables()
{
    // Initialise table array
    $xartable = array();
    // Name for webshare database entities
    $webshare = xarDBGetSiteTablePrefix() . '_webshare';
    // Table name
    $xartable['webshare'] = $webshare;
    // Return table information
    return $xartable;
}

?>
