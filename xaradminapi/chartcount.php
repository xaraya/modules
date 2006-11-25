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
 * for a pie chart
 *
 * @author Richard Cave
 * @param $args array
 * @param $args['type'] column count to retrieve
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sniffer_adminapi_chartcount($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if (!isset($type)) {
        $type = 'osnam';
    }

    // Security check
    if(!xarSecurityCheck('ReadSniffer')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set roles and categories table
    $snifferTable = $xartable['sniffer'];

    // Get count of items
    $query = "SELECT COUNT( xar_ua_".$type."),
                     xar_ua_".$type."
              FROM $snifferTable
              GROUP BY xar_ua_".$type;
    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($groupcount,
             $productname) = $result->fields;

        $items[] = array('groupcount' => $groupcount,
                         'productname' => $productname);
    }

    // Close result set
    $result->Close();

    // Return the items
    return $items;
}

?>
