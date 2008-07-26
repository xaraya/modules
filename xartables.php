<?php
/**
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

    $categories = xarDB::getPrefix() . '_categories';
    $categories_linkage = xarDB::getPrefix() . '_categories_linkage';
    $categories_basecategories = xarDB::getPrefix() . '_categories_basecategories';

    // Set the table name
    $xartable['categories'] = $categories;
    $xartable['categories_linkage'] = $categories_linkage;
    $xartable['categories_basecategories'] = $categories_basecategories;
    return $xartable;
}

?>