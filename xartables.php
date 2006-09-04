<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information for the tables
 */
function gallery_xartables()
{
    // Set module prefix for tables
    $prefix = "_gallery";

    // Initialise table array
    $xartable = array();

    $table = xarDBGetSiteTablePrefix() . $prefix;

    $xartable['gallery_albums'] = $table;
    $xartable['gallery_album_settings'] = $table . '_settings';
    $xartable['gallery_files'] = $table . '_files';
    $xartable['gallery_files_linkage'] = $table . '_files_linkage';

    // Return the table information
    return $xartable;
}
?>