<?php // 
// ----------------------------------------------------------------------
// Xaraya Content Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Chris Dudley
// Purpose of file:  Table information for pubsub module
// ----------------------------------------------------------------------

function pubsub_pntables()
{
    // Initialise table array
    $pntable = array();

    // Name for pubsub events database entities
    $pubsub_events = pnConfigGetVar('prefix') . '_pubsub_events';

    // Table name
    $pntable['pubsub_events'] = $pubsub_events;

    // Name for pubsub event registration database entities
    $pubsub_reg = pnConfigGetVar('prefix') . '_pubsub_reg';

    // Table name
    $pntable['pubsub_reg'] = $pubsub_reg;

    // Name for pubsub event handling database entities
    $pubsub_process = pnConfigGetVar('prefix') . '_pubsub_process';

    // Table name
    $pntable['pubsub_process'] = $pubsub_process;

    // Return table information
    return $pntable;
}

?>
