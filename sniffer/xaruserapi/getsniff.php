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
 * Get a sniffer item
 *  
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id']  id of the sniff item
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sniffer_userapi_getsniff($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'id', 'userapi', 'getsniff', 'sniffer');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
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
              WHERE xar_ua_id = " . xarVarPrepForStore($id);
    $result = $dbconn->Execute($query);

    // Check for an error
    if (!$result) return;

    // Put items into result array
    list($id,
         $agent,
         $osnam,
         $osver,
         $agnam,
         $agver,
         $cap,
         $quirk) = $result->fields;

    // Close result set
    $result->Close();

    $sniff = array('id' => $id,
                   'agent' => $agent,
                   'osnam' => $osnam,
                   'osver' => $osver,
                   'agnam' => $agnam,
                   'agver' => $agver,
                   'cap' => $cap,
                   'quirk' => $quirk);


    // Return the sniff
    return $sniff;
}

?>
