<?php

/**
 * return the field names and correct values for joining on ratings table
 * example : SELECT ..., $moduleid, $itemid, $rating,...
 *           FROM ...
 *           LEFT JOIN $table
 *               ON $field = <name of itemid field>
 *           WHERE ...
 *               AND $rating > 1000
 *               AND $where
 *
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] optional array of itemids that we are selecting on
 * @returns array
 * @return array('table' => 'nuke_ratings',
 *               'field' => 'nuke_ratings.xar_itemid',
 *               'where' => "nuke_ratings.xar_itemid IN (...)
 *                           AND nuke_ratings.xar_moduleid = 123",
 *               'moduleid'  => 'nuke_ratings.xar_moduleid',
 *               ...
 *               'rating'  => 'nuke_ratings.xar_rating')
 */
function ratings_userapi_leftjoin($args)
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
            // Security Check
// FIXME: add some instances here
            if(!xarSecurityCheck('OverviewRatings')) return;
        }
    } else {
        if(!xarSecurityCheck('OverviewRatings')) return;
    }

    // Table definition
    $xartable =& xarDBGetTables();
    $userstable = $xartable['ratings'];

    $leftjoin = array();

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    $leftjoin['table'] = $xartable['ratings'];
    $leftjoin['field'] = '';
    if (!empty($modid)) {
        $leftjoin['field'] .= $xartable['ratings'] . ".xar_moduleid = '" .
                             xarVarPrepForStore($modid) . "'";
        $leftjoin['field'] .= ' AND ';
    }
    if (!empty($itemtype)) {
        $leftjoin['field'] .= $xartable['ratings'] . ".xar_itemtype = '" .
                             xarVarPrepForStore($itemtype) . "'";
        $leftjoin['field'] .= ' AND ';
    }
    $leftjoin['field'] .= $xartable['ratings'] . '.xar_itemid';

    if (count($itemids) > 0) {
        $allids = join(', ', $itemids);
        $leftjoin['where'] = $xartable['ratings'] . '.xar_itemid IN (' .
                             xarVarPrepForStore($allids) . ')';
/*
        if (!empty($modname)) {
            $leftjoin['where'] .= ' AND ' .
                                  $xartable['ratings'] . ".xar_moduleid = '" .
                                  xarVarPrepForStore($modid) . "'";
        }
        if (!empty($itemtype)) {
            $leftjoin['where'] .= ' AND ' .
                                  $xartable['ratings'] . ".xar_itemtype = '" .
                                  xarVarPrepForStore($itemtype) . "'";
        }
*/
    } else {
        $leftjoin['where'] = '';
/*
        if (!empty($modid)) {
            $leftjoin['where'] = $xartable['ratings'] . ".xar_moduleid = '" .
                                 xarVarPrepForStore($modid) . "'";
        } else {
            $leftjoin['where'] = '';
        }
        if (!empty($itemtype)) {
            $leftjoin['where'] = $xartable['ratings'] . ".xar_itemtype = '" .
                                 xarVarPrepForStore($itemtype) . "'";
        } else {
            $leftjoin['where'] = '';
        }
*/
    }

    // Add available columns in the ratings table
    $columns = array('moduleid','itemtype','itemid','rating','numratings');
    foreach ($columns as $column) {
        $leftjoin[$column] = $xartable['ratings'] . '.xar_' . $column;
    }

    return $leftjoin;
}

?>
