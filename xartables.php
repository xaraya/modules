<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: St.Ego
// Purpose of file:  Table information for example module
// ----------------------------------------------------------------------

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function xtasks_xartables()
{
    $xartable = array();
    $xtasks = xarDBGetSiteTablePrefix() . '_xtasks';
    $xartable['xtasks'] = $xtasks;
    return $xartable;
}
?>