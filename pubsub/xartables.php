<?php // 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------

function pubsub_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for pubsub events database entities
    $pubsub_events = xarDBGetSiteTablePrefix() . '_pubsub_events';

    // Table name
    $xartable['pubsub_events'] = $pubsub_events;

    // Name for pubsub event registration database entities
    $pubsub_reg = xarDBGetSiteTablePrefix() . '_pubsub_reg';

    // Table name
    $xartable['pubsub_reg'] = $pubsub_reg;

    // Name for pubsub event handling database entities
    $pubsub_process = xarDBGetSiteTablePrefix() . '_pubsub_process';

    // Table name
    $xartable['pubsub_process'] = $pubsub_process;

    // Name for pubsub template database entities
    $pubsub_template = xarDBGetSiteTablePrefix() . '_pubsub_template';

    // Table name
    $xartable['pubsub_template'] = $pubsub_template;

    // Return table information
    return $xartable;
}

?>
