<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
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