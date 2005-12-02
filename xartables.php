<?php
/**
* Generate list of database tables we use
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * Return ebulletin table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function ebulletin_xartables()
{
    $prefix = xarDBGetSiteTablePrefix();
    $base = 'ebulletin';

    $xarTables = array();
    $xarTables["{$base}"] = "{$prefix}_{$base}";
    $xarTables["{$base}_issues"] = "{$prefix}_{$base}_issues";
    $xarTables["{$base}_subscriptions"] = "{$prefix}_{$base}_subscriptions";

    // Return the table information
    return $xarTables;
}

?>
