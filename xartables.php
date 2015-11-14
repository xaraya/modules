<?php 
/**
 * Release Table definitions
 *
 * @package modules
 * @subpackage Release Module
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 */
/**
 * Table definitions
 * 
 * This function is called when Xaraya searches for the tables in this module
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */

function release_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the release id table
    $xartable['releases'] = xarDB::getPrefix() . '_release_releases';

    // Get the name for the release notification table
    $xartable['release_notes'] = xarDB::getPrefix() . '_release_notes';

    // Get the name for the release documentation table
    $xartable['release_docs'] = xarDB::getPrefix() . '_release_docs';

    // Return the table information
    return $xartable;
}

?>