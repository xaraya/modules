<?php // 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
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
