<?php 
// File: $Id: s.xartables.php 1.5 02/11/28 18:37:07-06:00 strat@stratagem.com $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Gregor J. Rothfuss
// Purpose of file:  Table information for trackback module
// ----------------------------------------------------------------------

function trackback_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for trackback database entities
    $trackback = xarDBGetSiteTablePrefix() . '_trackback';

    // Table name
    $xartable['trackback'] = $trackback;

    // Return table information
    return $xartable;
}

?>
