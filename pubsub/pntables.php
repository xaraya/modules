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
    $pubsub_events = pnDBGetSiteTablePrefix() . '_pubsub_events';

    // Table name
    $pntable['pubsub_events'] = $pubsub_events;

    // Name for pubsub event registration database entities
    $pubsub_reg = pnDBGetSiteTablePrefix() . '_pubsub_reg';

    // Table name
    $pntable['pubsub_reg'] = $pubsub_reg;

    // Name for pubsub event handling database entities
    $pubsub_process = pnDBGetSiteTablePrefix() . '_pubsub_process';

    // Table name
    $pntable['pubsub_process'] = $pubsub_process;

    // Name for pubsub template database entities
    $pubsub_template = pnDBGetSiteTablePrefix() . '_pubsub_template';

    // Table name
    $pntable['pubsub_template'] = $pubsub_template;

    // Return table information
    return $pntable;
}

?>
