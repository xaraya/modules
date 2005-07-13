<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for example module
// ----------------------------------------------------------------------

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function xproject_xartables()
{
    $xartable = array();
    $xproject = xarDBGetSiteTablePrefix() . '_xproject';
    $xartable['xproject'] = $xproject;
    $xproject_tasks = xarDBGetSiteTablePrefix() . '_xproject_tasks';
    $xartable['xproject_tasks'] = $xproject_tasks;
    return $xartable;
}
?>