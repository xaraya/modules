<?php
// File: $Id: s.xartables.php 1.7 02/11/28 18:37:07-06:00 strat@stratagem.com $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Modified by: Nuncanada
// Purpose of file:  Initialisation functions for categories
// ----------------------------------------------------------------------

function categories_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for categories
    $categories=  xarDBGetSiteTablePrefix() . '_categories';

    // Table name
    $xartable['categories'] = $categories;

    // Column names
    $xartable['categories_column'] = array('cid'         => $categories . '.xar_cid',
                                          'name'        => $categories . '.xar_name',
                                          'description' => $categories . '.xar_description',
                                          'image'       => $categories . '.xar_image',
                                          'parent'      => $categories . '.xar_parent',
                                          'left'        => $categories . '.xar_left',
                                          'right'       => $categories . '.xar_right');
                                            
    // Clean names, necessarry for self-join statements
    $xartable['categories_column_clean'] = array('cid'         => 'xar_cid',
                                                'name'        => 'xar_name',
                                                'description' => 'xar_description',
                                                'image'       => 'xar_image',
                                                'parent'      => 'xar_parent',
                                                'left'        => 'xar_left',
                                                'right'       => 'xar_right');

    // Name for linkage
    $categories_linkage =  xarDBGetSiteTablePrefix() . '_categories_linkage';

    // Table name
    $xartable['categories_linkage'] = $categories_linkage;

    // Column names
    $xartable['categories_linkage_column'] = array('cid' => $categories_linkage . '.xar_cid',
                                                  'iid' => $categories_linkage . '.xar_iid',
                                                  'modid' => $categories_linkage . '.xar_modid');

    // Return table information
    return $xartable;
}

?>