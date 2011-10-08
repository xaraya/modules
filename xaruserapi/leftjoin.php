<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Jim McDonald
 */
/**
 * return the field names and correct values for joining on ratings table
 * example : SELECT ..., $module_id, $itemid, $rating,...
 *           FROM ...
 *           LEFT JOIN $table
 *               ON $field = <name of itemid field>
 *           WHERE ...
 *               AND $rating > 1000
 *               AND $where
 *
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['itemtype'] item type (optional) or array of itemtypes
 * @param $args['itemids'] optional array of itemids that we are selecting on
 * @return array('table' => '[SitePrefix]_ratings',
 *               'field' => '[SitePrefix]_ratings.itemid',
 *               'where' => "[SitePrefix]_ratings.itemid IN (...)
 *                           AND [SitePrefix]_ratings.module_id = 123",
 *               'module_id'  => '[SitePrefix]_ratings.module_id',
 *               ...
 *               'rating'  => '[SitePrefix]_ratings.rating')
 */
function ratings_userapi_leftjoin($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (!isset($modname)) {
        $modname = '';
    } else {
        $modid = xarMod::getRegID($modname);
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
    $xartable =& xarDB::getTables();
    $userstable = $xartable['ratings'];

    $leftjoin = array();

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    $leftjoin['table'] = $xartable['ratings'];
    $leftjoin['field'] = '';
    if (!empty($modid)) {
        $leftjoin['field'] .= $xartable['ratings'] . ".module_id = " . $modid;
        $leftjoin['field'] .= ' AND ';
    }
    if (!empty($itemtype)) {
        if (is_numeric($itemtype)) {
            $leftjoin['field'] .= $xartable['ratings'] . '.itemtype = ' . $itemtype;
            $leftjoin['field'] .= ' AND ';
        } elseif (is_array($itemtype) && count($itemtype) > 0) {
            $seentype = array();
            foreach ($itemtype as $id) {
                if (empty($id) || !is_numeric($id)) continue;
                $seentype[$id] = 1;
            }
            if (count($seentype) == 1) {
                $itemtypes = array_keys($seentype);
                $leftjoin['field'] .= $xartable['ratings'] . '.itemtype = ' . $itemtypes[0];
                $leftjoin['field'] .= ' AND ';
            } elseif (count($seentype) > 1) {
                $itemtypes = join(', ', array_keys($seentype));
                $leftjoin['field'] .= $xartable['ratings'] . '.itemtype IN (' . $itemtypes . ')';
                $leftjoin['field'] .= ' AND ';
            }
        }
    }
    $leftjoin['field'] .= $xartable['ratings'] . '.itemid';

    if (count($itemids) > 0) {
        $allids = join(', ', $itemids);
        $leftjoin['where'] = $xartable['ratings'] . '.itemid IN (' . $allids . ')';
    } else {
        $leftjoin['where'] = '';
    }

    // Add available columns in the ratings table
    $columns = array('module_id','itemtype','itemid','rating','numratings');
    foreach ($columns as $column) {
        $leftjoin[$column] = $xartable['ratings'] . '.' . $column;
    }
    return $leftjoin;
}
?>
