<?php
/**
 * File: $Id$
 *
 * Trackback User API
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage trackback
 * @author Gregor J. Rothfuss
 */

/**
 * Get a trackback for a specific item
 *
 * @param string $args['modname'] name of the module this trackback is for
 * @param int $args['objectid'] ID of the item this trackback is for
 * @return int hits the corresponding hit count, or void if no hit exists
 */
function trackback_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($objectid))) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Security check
    if (!xarSecAuthAction(0, 'Trackback::', "$modname::$objectid", ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $trackbacktable = $xartable['trackback'];

    // TODO: add item type

    // Get items
    $query = "SELECT xar_url, xar_blog_name, xar_title, xar_excerpt
            FROM $trackbacktable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
              AND xar_itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $res['url'] = $result->fields[0];
    $res['blogname'] = $result->fields[1];
    $res['title'] = $result->fields[2];
    $res['exerpt'] = $result->fields[3];
    $result->close();

    return $res;
}

/**
 * Get a trackback for a list of items
 *
 * @param string $args['modname'] name of the module you want items from
 * @param array $args['itemids'] array of item IDs
 * @return array $array[$itemid] = $urls;
 */
function trackback_userapi_getitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    if (!isset($itemids)) {
        $itemids = array();
    }

    // Security check
    if (count($itemids) > 0) {
        foreach ($itemids as $itemid) {
            if (!xarSecAuthAction(0, 'Trackback::', "$modname::$itemid", ACCESS_OVERVIEW)) {
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
                return;
            }
        }
    } else {
        if (!xarSecAuthAction(0, 'Trackback::', "$modname::", ACCESS_OVERVIEW)) {
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
            return;
        }
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $trackbacktable = $xartable['trackback'];

    // Get items
    $query = "SELECT xar_itemid, xar_url, xar_title, xar_excerpt
            FROM $trackbacktable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'";
    if (count($itemids) > 0) {
        $allids = join(', ',$itemids);
        $query .= " AND xar_itemid IN ('" . xarVarPrepForStore($allids) . "')";
    }
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $tblist = array();
    while (!$result->EOF) {
        list($id,$url, $title, $exerpt) = $result->fields;
        $tblist[$id] = $url;
        $result->MoveNext();
    }
    $result->close();

    return $tblist;
}

/**
 * Get the list of modules for which we're counting items
 *
 * @return array $array[$modid] = $numitems
 */
function trackback_userapi_getmodules($args)
{
    // Security check
    if (!xarSecAuthAction(0, 'Trackback::', "::", ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $trackbacktable = $xartable['trackback'];

    // Get items
    $query = "SELECT xar_moduleid, COUNT(xar_itemid)
            FROM $trackbacktable
            GROUP BY xar_moduleid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$numitems) = $result->fields;
        $modlist[$modid] = $numitems;
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

/**
 * Return the field names and correct values for joining on trackback table
 *
 * example : SELECT ..., $moduleid, $itemid, $hits,...
 *           FROM ...
 *           LEFT JOIN $table
 *               ON $field = <name of itemid field>
 *           WHERE ...
 *               AND $hits > 1000
 *               AND $where
 *
 * @param string $args['modname'] name of the module you want items from, or
 * @param int $args['modid'] ID of the module you want items from
 * @param array $args['itemids'] optional array of itemids that we are selecting on
 * @return array('table' => 'xar_trackback',
 *               'field' => 'xar_trackback.xar_itemid',
 *               'where' => 'xar_trackback.xar_itemid IN (...)
 *                           AND xar_trackback.xar_moduleid = 123',
 *               'moduleid'  => 'xar_trackback.xar_moduleid',
 *               ...
 *               'urls'  => 'xar_trackback.xar_url')
 */
function trackback_userapi_leftjoin($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (!isset($modname)) {
        $modname = '';
    } else {
        $modid = xarModGetIDFromName($modname);
    }
    if (!isset($modid)) {
        $modid = '';
    }
    if (!isset($itemids)) {
        $itemids = array();
    }

    // Security check
    if (count($itemids) > 0) {
        foreach ($itemids as $itemid) {
            if (!xarSecAuthAction(0, 'Trackback::', "$modname::$itemid", ACCESS_OVERVIEW)) {
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
                return;
            }
        }
    } else {
        if (!xarSecAuthAction(0, 'Trackback::', "$modname::", ACCESS_OVERVIEW)) {
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
            return;
        }
    }

    // Table definition
    $xartable = xarDBGetTables();
    $userstable = $xartable['trackback'];

    $leftjoin = array();

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    $leftjoin['table'] = $xartable['trackback'];
    if (!empty($modid)) {
        $leftjoin['field'] = $xartable['trackback'] . '.xar_moduleid = ' . $modid;
        $leftjoin['field'] .= ' AND ' . $xartable['trackback'] . '.xar_itemid';
    } else {
        $leftjoin['field'] = $xartable['trackback'] . '.xar_itemid';
    }

    if (count($itemids) > 0) {
        $allids = join(', ', $itemids);
        $leftjoin['where'] = $xartable['trackback'] . '.xar_itemid IN (' .
                             xarVarPrepForStore($allids) . ')';
/*
        if (!empty($modid)) {
            $leftjoin['where'] .= ' AND ' .
                                  $xartable['trackback'] . '.xar_moduleid = ' .
                                  $modid;
        }
*/
    } else {
/*
        if (!empty($modid)) {
            $leftjoin['where'] = $xartable['trackback'] . '.xar_moduleid = ' .
                                 $modid;
        } else {
            $leftjoin['where'] = '';
        }
*/
        $leftjoin['where'] = '';
    }

    // Add available columns in the trackback table
    $columns = array('moduleid','itemid','urls');
    foreach ($columns as $column) {
        $leftjoin[$column] = $xartable['trackback'] . '.xar_' . $column;
    }

    return $leftjoin;
}

?>
