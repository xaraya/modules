<?php 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for release module
// ----------------------------------------------------------------------

function release_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the release id table
    $releaseid = xarConfigGetVar('prefix') . '_release_id';

    // Get the name for the release notification table
    $releasenotes = xarConfigGetVar('prefix') . '_release_notes';

    // Get the name for the release documentation table
    $releasedocs = xarConfigGetVar('prefix') . '_release_docs';

    // Set the table name
    $xartable['release_id']     = $releaseid;
    $xartable['release_notes']   = $releasenotes;
    $xartable['release_docs']   = $releasedocs;

    // Return the table information
    return $xartable;
}

?>