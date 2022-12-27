<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Table information
 *
 */

function sitemapper_xartables()
{
    // Initialise table array
    $xartable = [];

    $xartable['sitemapper_links'] = xarDB::getPrefix() . '_sitemapper_links';
    $xartable['sitemapper_maps'] = xarDB::getPrefix() . '_sitemapper_maps';
    $xartable['sitemapper_engines'] = xarDB::getPrefix() . '_sitemapper_engines';
    $xartable['sitemapper_sources'] = xarDB::getPrefix() . '_sitemapper_sources';

    // Return the table information
    return $xartable;
}
