<?php
// $Id$
// ----------------------------------------------------------------------
// POSTNUKE Content Management System
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
// Original Author of file: voll
// Purpose of file:  Table information for nameday module
// ----------------------------------------------------------------------

function nameday_pntables()
{
    // Initialise table array
    $pntable = array();

    // Name for nameday database entities
    $nameday = pnConfigGetVar('prefix') . '_nameday';

    // Table name
    $pntable['nameday'] = $nameday;

    // Column names
    $pntable['nameday_column'] = array ('ndid'   => $nameday . '.pn_id',
                                  'did'       => $nameday . '.pn_did',
                                  'mid'       => $nameday . '.pn_mid',
                                  'content'   => $nameday . '.pn_content',
                                  'ndlanguage'  => $nameday . '.pn_language');
    // Return table information
    return $pntable;
}
?>