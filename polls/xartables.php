<?php
// File: $Id: s.xartables.php 1.4 03/01/01 15:16:38-06:00 dracos@numenor. $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for Polls module
// ----------------------------------------------------------------------

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function polls_xartables()
{
    // Initialise table array
    $xartable = array();

    $polls = xarConfigGetVar('prefix') . '_polls';
    $xartable['polls'] = $polls;

    $pollsinfo = xarConfigGetVar('prefix') . '_polls_info';
    $xartable['polls_info'] = $pollsinfo;

    // Return the table information
    return $xartable;
}

?>