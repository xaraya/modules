<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 *
 * Xaraya Referers
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al.
 */

/**
 * example event handler for the system event ServerRequest
 *
 * this function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author the Example module development team
 * @returns bool
 */
function referer_eventapi_OnServerRequest()
{
    $HTTP_REFERER = getenv('HTTP_REFERER');
    $HTTP_HOST = getenv('HTTP_HOST');

    if (empty($HTTP_HOST)) {
        $HTTP_HOST = 'http://' . $_SERVER['HTTP_HOST'];
        $HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    /**
     * Check to make sure that we do not log our own domain.
     */

    if (!ereg("$HTTP_HOST", $HTTP_REFERER)) {
        if ($HTTP_REFERER == '') {
            $HTTP_REFERER = 'bookmark';
        }
        // Get database setup
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        $referertable = $xartable['referer'];
        // Check to see if the referer is already in DB
        $query = "SELECT count(xar_rid) as c
                  FROM $referertable
                  WHERE xar_url = '" . $HTTP_REFERER . "'";
        $result = &$dbconn->Execute($query);

        $row = $result->fields;
        $count = $row[0];

        if ($count == 1) {
            $query = "UPDATE $referertable
                      SET xar_frequency = xar_frequency + 1
                      WHERE xar_url = '" . $HTTP_REFERER . "'";
        } else {
            // Get next ID in table
            $nextId = $dbconn->GenId($referertable);
            $query = "INSERT INTO $referertable(
                                       xar_rid,
                                       xar_url,
                                       xar_frequency)
                           VALUES(
                                       $nextId,
                                       '" . xarVarPrepForStore($HTTP_REFERER) . "',
                                       1)";
        }

        $result = &$dbconn->Execute($query);
        if (!$result) return;
    }
}

?>