<?php 
/**
 * File: $Id$
 * 
 * Xaraya Autolinks
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage xarCacheManager Module
 * @author jsb 
*/

function xarcachemanager_xartables()
{
    // Initialise table array
    $xartable = array();

    // Set the table names
    $xartable['cache_blocks'] = xarDBGetSiteTablePrefix() . '_cache_blocks'; // cfr. blocks module
    $xartable['cache_data'] = xarDBGetSiteTablePrefix() . '_cache_data';

    // Return the table information
    return $xartable;
}

?>
