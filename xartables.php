<?php 
/**
 * Release Table definitions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 */
/**
 * initialization functions
 * Initialise the Release module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */

function release_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the release id table
    $releaseid = xarConfigGetVar('prefix') . '_release_id';

    // Get the name for the release notification table
    $releasenotes = xarConfigGetVar('prefix') . '_release_notes';

    // Get the name for the release documentation table
    $releasedocs = xarConfigGetVar('prefix') . '_release_docs';

    // Set the table name
    $xartable['release_id']     = $releaseid;
    $xartable['release_notes']   = $releasenotes;
    $xartable['release_docs']   = $releasedocs;

    // Return the table information
    return $xartable;
}

?>