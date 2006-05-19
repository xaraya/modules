<?php
/**
 * Maxercalls table definitions function
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @link http://xaraya.com/index.php/release/247.html
 * @author maxercalls module development team
 */
/**
 * Return maxercalls table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @access private
 * @return array with table names
 */
function maxercalls_xartables()
{
    // Initialise table array
    $xarTables = array();
    // Set the table name
    $xarTables['maxercalls'] = xarDBGetSiteTablePrefix() . '_maxercalls';
    $xarTables['maxercalls_types'] = xarDBGetSiteTablePrefix() . '_maxercalls_types';
    $xarTables['maxercalls_maxers'] = xarDBGetSiteTablePrefix() . '_maxercalls_maxers';
    $xarTables['maxercalls_maxerstatus'] = xarDBGetSiteTablePrefix() . '_maxercalls_maxerstatus';
    $xarTables['maxercalls_maxerfunction'] = xarDBGetSiteTablePrefix() . '_maxercalls_maxerfunction';
    // Return the table information
    return $xarTables;
}
?>
