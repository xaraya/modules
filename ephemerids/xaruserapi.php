<?php
/**
 * File: $Id$
 * 
 * Ephemerids
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

/**
 * get all Ephemerids
 *
 * @author the Ephemerids module development team
 * @param numitems the number of items to retrieve (default -1 = all)
 * @param startnum start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ephemerids_userapi_getall($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if ($startnum == "") {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid aurguments',
                    join(', ',$invalid), 'user', 'getall', 'ephemerids');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $items = array();

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "SELECT xar_eid,
                     xar_did,
                     xar_mid, 
                     xar_yid,
                     xar_content,
                     xar_elanguage
            FROM $ephemtable
            ORDER BY xar_yid";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put items into result array. 
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name, $number) = $result->fields;
        if (xarSecurityCheck('OverviewEphemerids', 0)) {
            $items[] = array('eid' => $eid,
                  'did' => $did,
                  'mid' => $mid,
                  'yid' => $yid,
                  'content' => $content,
                  'elanguage' => $elanguage);
        }
    }

    $result->Close();

    // Return the items
    return $items;
}

/**
 * get all Ephemerids
 *
 * @author the Ephemerids module development team
 * @param numitems the number of items to retrieve (default -1 = all)
 * @param startnum start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ephemerids_userapi_getalltoday()
{
    $items = array();

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $today = getdate();
    $eday = $today['mday'];
    $emonth = $today['mon'];

    $query = "SELECT xar_eid,
                     xar_did,
                     xar_mid, 
                     xar_yid,
                     xar_content,
                     xar_elanguage
            FROM $ephemtable
            WHERE xar_did='".xarVarPrepForStore($eday)."' AND xar_mid='".xarVarPrepForStore($emonth)."'";
    $result =& $dbconn->SelectLimit($query);
    if (!$result) return;

    // Put items into result array. 
    for (; !$result->EOF; $result->MoveNext()) {
        list($eid, $did, $mid, $yid, $content, $elanguage) = $result->fields;
        if (xarSecurityCheck('OverviewEphemerids', 0)) {
            $items[] = array(
                  'did' => $did,
                  'mid' => $mid,
                  'yid' => $yid,
                  'content' => $content);
        }
    }

    $result->Close();

    // Return the items
    return $items;
}

/**
 * get a specific item
 *
 * @author the Ephemerids
 * @param $args['eid'] id of ephemerid
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ephemerids_userapi_get($args)
{
    // Get arguments 
    extract($args);

    // Argument check 
    if (!isset($eid) || !is_numeric($eid)) {
        $msg = xarML('Invalid parameter',
                    'item ID', 'user', 'get', 'ephemerids');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    // Get item
    $query = "SELECT xar_did, 
                     xar_mid, 
                     xar_yid,
                     xar_content,
                     xar_elanguage
            FROM $ephemtable
            WHERE xar_eid = " . xarVarPrepForStore($eid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exists');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Obtain the item information from the result set
    list($did, $mid, $yid, $content, $elanguage) = $result->fields;

    $result->Close();

    // Security Check
    if(!xarSecurityCheck('OverviewEphemerids')) return;

    // Create the item array
    $data = array('eid' => $eid,
                  'did' => $did,
                  'mid' => $mid,
                  'yid' => $yid,
                  'content' => $content,
                  'elanguage' => $elanguage);

    // Return the item array
    return $data;
}

/**
 * utility function to count the number of items held by this module
 *
 * @author the Ephemerid 
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function ephemerids_userapi_countitems()
{
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Security Check
    if(!xarSecurityCheck('OverviewEphemerids')) return;

    $ephemtable = $xartable['ephem'];

    // Get item 
    $query = "SELECT COUNT(1)
            FROM $ephemtable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Obtain the number of items
    list($numitems) = $result->fields;

    $result->Close();

    // Return the number of items
    return $numitems;
}

?>