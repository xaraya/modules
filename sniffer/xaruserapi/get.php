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
 * Get all sniffer items
 *  
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['numitems'] the number of items to retrieve (default -1 = all)
 * @param $args['startnum'] start with this item number (default 1)
 * @param $args['sortby'] sort by columns in table
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sniffer_userapi_get($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if (!isset($sortby) || !is_string($sortby)) {
        $invalid[] = 'id';
    }

    if(!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = -1;
    }
    
    // Security check
    if(!xarSecurityCheck('ReadSniffer')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set roles and categories table
    $snifferTable = $xartable['sniffer'];

    // Get items
    $query = "SELECT xar_ua_id,
                     xar_ua_agent,
                     xar_ua_osnam,
                     xar_ua_osver,
                     xar_ua_agnam,
                     xar_ua_agver,
                     xar_ua_cap,
                     xar_ua_quirk
              FROM $snifferTable
              ORDER BY xar_ua_" . $sortby;
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $agent,
             $osnam,
             $osver,
             $agnam,
             $agver,
             $cap,
             $quirk) = $result->fields;

        $items[] = array('id' => $id,
                         'agent' => $agent,
                         'osnam' => $osnam,
                         'osver' => $osver,
                         'agnam' => $agnam,
                         'agver' => $agver,
                         'cap' => $cap,
                         'quirk' => $quirk);
    }

    // Close result set
    $result->Close();

    // Return the items
    return $items;
}

?>
