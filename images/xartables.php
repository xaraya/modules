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
// Purpose of file:  Table information for images module
// ----------------------------------------------------------------------

function images_xartables()
{
    // Initialise table array
    $pntable = array();

    // Name for images database entities
    $images = pnConfigGetVar('prefix') . '_images';

    // Table name
    $pntable['images'] = $images;

    // Column names
    $pntable['images_column'] = array('iid'         => $images . '.pn_iid',
                                      'title'       => $images . '.pn_title',
                                      'description' => $images . '.pn_description',
                                      'format'      => $images . '.pn_format',
                                      'file'        => $images . '.pn_file');


    // Return table information
    return $pntable;
}

?>
