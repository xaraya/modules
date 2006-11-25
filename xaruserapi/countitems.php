<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
/**
 * Utility function to count the number of items
 *
 * @author Richard Cave
 * @param void
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV: With no parameters to process, why have the $args in here?
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
