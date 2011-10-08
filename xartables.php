<?php
/**
 * Shouter Module
 *
 * @package modules
 * @subpackage shouter module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Return the table names of this module
 */
function shouter_xartables()
{
    $tables = array();
    $tables['shouter'] = xarDB::getPrefix() . '_shouter';

    return $tables;
}
?>