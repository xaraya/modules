<?php 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for autolinks module
// ----------------------------------------------------------------------

function release_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the autolinks item table
    $releaseid = xarConfigGetVar('prefix') . '_release_id';

    // Set the table name
    $xartable['release_id'] = $releaseid;

    // Return the table information
    return $xartable;
}

?>