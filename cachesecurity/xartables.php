<?php 
/**
 * File: $Id$
 * 
 * Xaraya's CacheSecurity Module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage CacheSecurity Module
 * @author Flavio Botelho <nuncanada@xaraya.com>
*/

function cachesecurity_xartables()
{
    // Initialise table array
    $tables = array();
    $sitePrefix = xarDBGetSiteTablePrefix();

    $tables['security_cache_privileges'] = $sitePrefix . '_seccache_privileges';
    $tables['security_cache_masks']      = $sitePrefix . '_seccache_masks';

    $tables['security_cache_rolesgraph']      = $sitePrefix . '_seccache_rolesgraph';
    $tables['security_cache_privsgraph']      = $sitePrefix . '_seccache_privsgraph';

    // Return the table information
    return $tables;
}

?>
