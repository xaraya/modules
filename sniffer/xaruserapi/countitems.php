<?php
/**
 * File: $Id$
 *
 * Sniffer Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Sniffer Module
 * @author Frank Besler
 *
 * Using phpSniffer by Roger Raymond
 * Purpose of file: find out the browser and OS of the visitor
*/

/**
 * Utility function to count the number of items
 *  
 * @author Richard Cave
 * @param nada 
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sniffer_userapi_countitems($args)
{
    // Get arguments
    extract($args);

    // Security check
    if(!xarSecurityCheck('ReadSniffer')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set roles and categories table
    $snifferTable = $xartable['sniffer'];

    // Get count of items
    $query = "SELECT COUNT(1)
              FROM $snifferTable";

    $result =& $dbconn->Execute($query);
    
    // Check for an error
    if (!$result) return;

    // Obtain the number of items
    list($numitems) = $result->fields;

    // Close result set
    $result->Close();

    // Return the number of items
    return $numitems;
}

?>
