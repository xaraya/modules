<?php
// $Id$
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
// Original Author of file: Jim McDonald
// Purpose of file:  Initialisation functions for images
// ----------------------------------------------------------------------

/**
 * initialise the images module
 */
function images_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // Create tables
    $imagestable = $pntable['images'];
    $imagescolumn = &$pntable['images_column'];
    $sql = "CREATE TABLE $imagestable (
            $imagescolumn[iid] INT(10) NOT NULL AUTO_INCREMENT,
            $imagescolumn[title] TEXT,
            $imagescolumn[description] TEXT,
            $imagescolumn[format] TEXT NOT NULL,
            $imagescolumn[file] TEXT NOT NULL,
            PRIMARY KEY(pn_iid))";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed initialisation attempt
        return false;
    }

    // Set up module variables
    pnModSetVar('images', 'itemsperpage', 10);

    // Initialisation successful
    return true;
}

/**
 * upgrade the images module from an old version
 */
function images_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
        case 2.5:
            // Code to upgrade from version 2.5 goes here
            break;
    }
}

/**
 * delete the images module
 */
function images_delete()
{
    // Get database information
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // Delete tables
    $sql = "DROP TABLE $pntable[images]";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    // Delete module variables
    pnModDelVar('images', 'itemsperpage');

    // Deletion successful
    return true;
}

?>