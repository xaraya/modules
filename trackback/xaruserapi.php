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
    if (!xarSecurityCheck('ViewTrackBack', 1, 'TrackBack', "$modname:$objectid:All")) {
        return;
    }

    $modId = xarModGetIDFromName($modname);
    if (empty($modId)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // TODO: add item type

    // Get items
    $query = "SELECT url, blog_name, title, excerpt
            FROM $trackBackTable
            WHERE moduleid = '" . xarVarPrepForStore($modId) . "'
              AND itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $trackBack['url'] = $result->fields[0];
    $trackBack['blogname'] = $result->fields[1];
    $trackBack['title'] = $result->fields[2];
    $trackBack['exerpt'] = $result->fields[3];
    $result->close();

    return $trackBack;
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
    $modId = xarModGetIDFromName($modname);
    if (empty($modId)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    if (!isset($itemids)) {
        $itemids = array();
    }

    // Security check
    if (count($itemids) > 0) {
        foreach ($itemids as $itemid) {
            if (!xarSecurityCheck('ViewTrackBack', 1, 'TrackBack', "$modname:All:$itemid")) {
                return;
            }
        }
    } else {
            if (!xarSecurityCheck('ViewTrackBack', 1, 'TrackBack', "$modname:All:All")) {
                return;
            }
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // Get items
    $query = "SELECT itemid, url, title, excerpt
            FROM $trackBacktTable
            WHERE moduleid = '" . xarVarPrepForStore($modId) . "'";
    if (count($itemids) > 0) {
        $allIds = join(', ',$itemids);
        $query .= " AND itemid IN ('" . xarVarPrepForStore($allIds) . "')";
    }
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $tblist = array();
    while (!$result->EOF) {
        list($id,$url, $title, $exerpt) = $result->fields;
        $tbList[$id] = $url;
        $result->MoveNext();
    }
    $result->close();

    return $tbList;
}

/**
 * Get the list of modules for which we're counting items
 *
 * @return array $array[$modid] = $numitems
 */
function trackback_userapi_getmodules($args)
{
    // Security check
    if (!xarSecurityCheck('ViewTrackBack')) return;

    // Database information
    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // Get items
    $query = "SELECT moduleid, COUNT(itemid)
            FROM $trackBackTable
            GROUP BY moduleid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modList = array();
    while (!$result->EOF) {
        list($modId,$numItems) = $result->fields;
        $modList[$modId] = $numItems;
        $result->MoveNext();
    }
    $result->close();

    return $modList;
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
 * @return array('table' => 'trackback',
 *               'field' => 'trackbackid.itemid',
 *               'where' => 'trackbackid.itemid IN (...)
 *                           AND trackbackid.moduleid = 123',
 *               'moduleid'  => 'trackbackid.moduleid',
 *               ...
 *               'urls'  => 'trackbackid.xurl')
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

            if (!xarSecurityCheck('ViewTrackBack', 1, 'TrackBack', "$modname:All:$itemid")) {
                return;
            }
        }
    } else {
        if (!xarSecurityCheck('ViewTrackBack', 1, 'TrackBack', "$modname:All:All")) {
            return;
        }
    }

    // Table definition
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    $leftJoin = array();

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    $leftJoin['table'] = $tables['trackback'];
    if (!empty($modid)) {
        $leftJoin['field'] = $tables['trackback'] . '.moduleid = ' . $modid;
        $leftJoin['field'] .= ' AND ' . $tables['trackback'] . '.itemid';
    } else {
        $leftJoin['field'] = $tables['trackback'] . '.itemid';
    }

    if (count($itemids) > 0) {
        $allIds = join(', ', $itemids);
        $leftJoin['where'] = $tables['trackback'] . '.itemid IN (' .
                             xarVarPrepForStore($allIds) . ')';
/*
        if (!empty($modid)) {
            $leftJoin['where'] .= ' AND ' .
                                  $tables['trackback'] . '.moduleid = ' .
                                  $modid;
        }
*/
    } else {
/*
        if (!empty($modid)) {
            $leftJoin['where'] = $tables['trackback'] . '.moduleid = ' .
                                 $modid;
        } else {
            $leftJoin['where'] = '';
        }
*/
        $leftJoin['where'] = '';
    }

    // Add available columns in the trackback table
    $columns = array('moduleid','itemid','urls');
    foreach ($columns as $column) {
        $leftJoin[$column] = $tables['trackback'] . $column;
    }

    return $leftJoin;
}

?>