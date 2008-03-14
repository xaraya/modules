<?php

/**
 * File: $Id$
 *
 * Xarpages table definitions function.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

/**
 * Return xarpages table names to xaraya.
 *
 * @access private
 * @return array
 */

function xarpages_xartables()
{
    // Initialise table array.
    $xarTables = array();
    $basename = 'xarpages';

    // Loop for each table.
    foreach(array('pages', 'types') as $table) {
        // Set the table name.
        $xarTables[$basename . '_' . $table] = xarDB::getPrefix() . '_' . $basename . '_' . $table;
    }

    // Return the table information
    return $xarTables;
}

?>