<?php 

/**
 * File: $Id$
 *
 * Table definition file
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@hsdev.com>
*/

/**
 * This function is called internally by the core whenever the module is
 * loaded.
 */
function bkview_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the bkview table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $bkview = xarDBGetSiteTablePrefix() . '_bkview';

    // Set the table name
    $xartable['bkview'] = $bkview;

    // Return the table information
    return $xartable;
}

?>