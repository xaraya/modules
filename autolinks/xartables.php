<?php 
/**
 * File: $Id: s.xartables.php 1.11 03/07/13 11:22:48+02:00 marcel@hsdev.com $
 * 
 * Xaraya Autolinks
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Autolinks Module
 * @author Jim McDonald
*/

function autolinks_xartables()
{
    // Initialise table array
    $xartable = array();

    // Set the table names
    $xartable['autolinks'] = xarDBGetSiteTablePrefix() . '_autolinks';
    $xartable['autolinks_types'] = xarDBGetSiteTablePrefix() . '_autolinks_types';

    // Return the table information
    return $xartable;
}

?>
