<?php
/**
 * File: $Id: s.xarinit.php 1.22 03/01/26 20:03:00-05:00 John.Cox@mcnabb. $
 *
 * Categories System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage categories module
 * @author Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
*/

/**
 * specifies module tables namees
 *
 * @author  Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
 * @access  public
 * @param   none
 * @return  $xartable array
 * @throws  no exceptions
 * @todo    nothing
*/
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