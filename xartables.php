<?php 
// File: $Id: s.xartables.php 1.5 02/11/28 18:37:07-06:00 strat@stratagem.com $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for hitcount module
// ----------------------------------------------------------------------

function hitcount_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for hitcount database entities
    $hitcount = xarDBGetSiteTablePrefix() . '_hitcount';

    // Table name
    $xartable['hitcount'] = $hitcount;

    // Return table information
    return $xartable;
}

?>
