<?php // $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Volodymyr Metenchuk
// Purpose of file:  init for nameday module
// ----------------------------------------------------------------------

/**
 * init nameday module
 */
function nameday_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // Create tables
    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];
    $sql = "CREATE TABLE $namedaytable (
            $namedaycolumn[ndid] int(11) NOT NULL auto_increment,
            $namedaycolumn[did] tinyint(2) NOT NULL default '0',
            $namedaycolumn[mid] tinyint(2) NOT NULL default '0',
            $namedaycolumn[content] text NOT NULL,
            $namedaycolumn[ndlanguage] varchar(30) NOT NULL default '',
            PRIMARY KEY(pn_id))";

    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed initialisation attempt
        pnSessionSetVar('errormsg', xarML('Table creation failed'));
        return false;
    }

    // Set up module variables
    // pnModSetVar('nameday', 'detail', 0);

    // Initialisation successful
    return true;
}

/**
 * upgrade
 */
function nameday_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // upgrade versions
            break;
        case '1.3':
        case '1.3.0':
        case '2.0.0':
            // upgrade versions
            break;
        case '2.5.0':
            // upgrade versions
            break;
    }

    // Upgrade successful
    return true;
}

/**
 * delete the nameday module
 */
function nameday_delete()
{
    // Get database information
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // Delete tables
    $sql = "DROP TABLE $pntable[nameday]";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    // Delete module variables
    // pnModDelVar('nameday', 'detail');

    // Deletion successful
    return true;
}

?>