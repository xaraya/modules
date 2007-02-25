<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
    $tables['shouter'] = xarDBGetSiteTablePrefix() . '_shouter';

    return $tables;
}
?>