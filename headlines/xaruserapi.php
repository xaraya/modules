<?php 
/**
 * File: $Id$
 * 
 * Xaraya Headlines
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Headlines Module
 * @author John Cox
*/

/**
 * get all headlines
 * @returns array
 * @return array of links, or false on failure
 */

function headlines_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $links = array();

    // Security Check
	if(!xarSecurityCheck('OverviewHeadlines')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get links
    $query = "SELECT xar_hid,
                     xar_title,
                     xar_desc,
                     xar_url,
                     xar_order
            FROM $headlinestable
            ORDER BY xar_order";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($hid, $title, $desc, $url, $order) = $result->fields;
        if (xarSecurityCheck('OverviewHeadlines')) {
            $links[] = array('hid'      => $hid,
                             'title'    => $title,
                             'desc'     => $desc,
                             'url'      => $url,
                             'order'    => $order);
        }
    }

    $result->Close();

    return $links;
}

/**
 * get a specific headline
 * @poaram $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function headlines_userapi_get($args)
{
    extract($args);

    if (!isset($hid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'Headlines');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('OverviewHeadlines')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get link
    $query = "SELECT xar_hid,
                     xar_title,
                     xar_desc,
                     xar_url,
                     xar_order
            FROM $headlinestable
            WHERE xar_hid = " . xarVarPrepForStore($hid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($hid, $title, $desc, $url, $order) = $result->fields;
    $result->Close();

    $link = array('hid'     => $hid,
                  'title'   => $title,
                  'desc'    => $desc,
                   'url'     => $url,
                  'order'   => $order);

    return $link;
}

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function headlines_userapi_countitems()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Security Check
	if(!xarSecurityCheck('OverviewHeadlines')) return;

    $headlinestable = $xartable['headlines'];

    $query = "SELECT COUNT(1)
            FROM $headlinestable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>