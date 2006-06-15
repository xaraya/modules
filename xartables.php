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
    $xProjects = xarDBGetSiteTablePrefix() . '_xProjects';
    $xartable['xProjects'] = $xProjects;
    $xProject_features = xarDBGetSiteTablePrefix() . '_xProject_features';
    $xartable['xProject_features'] = $xProject_features;
    $xProject_pages = xarDBGetSiteTablePrefix() . '_xProject_pages';
    $xartable['xProject_pages'] = $xProject_pages;
    return $xartable;
}
?>