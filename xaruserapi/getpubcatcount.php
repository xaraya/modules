<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * get the number of publications per publication type and category
 *
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['ptid'] publication type ID
 * @param $args['cids'] array of category IDs (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['groupcids'] the number of categories you want items grouped by
 * @param $args['reverse'] default is ptid => cid, reverse (1) is cid => ptid
 * @return array array( $ptid => array( $cid => $count) ),
 *         or false on failure
 */
function publications_userapi_getpubcatcount($args)
{
    /*
        static $pubcatcount = array();

        if (count($pubcatcount) > 0) {
            return $pubcatcount;
        }
    */
    $pubcatcount = [];

    // Get database setup
    $dbconn = xarDB::getConn();

    // Get the LEFT JOIN ... ON ...  and WHERE parts from publications
    $publicationsdef = xarMod::apiFunc('publications', 'user', 'leftjoin', $args);

    // Load API
    if (!xarMod::apiLoad('categories', 'user')) {
        return;
    }

    $args['modid'] = xarMod::getRegID('publications');
    if (isset($args['ptid']) && !isset($args['itemtype'])) {
        $args['itemtype'] = $args['ptid'];
    }
    // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
    $categoriesdef = xarMod::apiFunc('categories', 'user', 'leftjoin', $args);

    // Get count
    $query = 'SELECT '. $publicationsdef['pubtype_id'] .', '. $categoriesdef['category_id']
           .', COUNT(*)
            FROM '. $publicationsdef['table'] . '
            LEFT JOIN ' . $categoriesdef['table'] .'
            ON '. $categoriesdef['field'] . ' = ' . $publicationsdef['field'] .
            $categoriesdef['more'] . '
            WHERE '. $categoriesdef['where'] .' AND '. $publicationsdef['where'] .'
            GROUP BY '. $publicationsdef['pubtype_id'] .', '. $categoriesdef['category_id'];

    $result = $dbconn->Execute($query);
    if (!$result) {
        return;
    }
    if ($result->EOF) {
        if (!empty($args['ptid']) && empty($args['reverse'])) {
            $pubcatcount[$args['ptid']] = [];
        }
        return $pubcatcount;
    }
    while (!$result->EOF) {
        // we may have 1 or more cid fields here, depending on what we're
        // counting (cfr. AND in categories)
        $fields = $result->fields;
        $ptid = array_shift($fields);
        $count = array_pop($fields);
        // TODO: use multi-level array for multi-category grouping ?
        $cid = join('+', $fields);
        if (empty($args['reverse'])) {
            $pubcatcount[$ptid][$cid] = $count;
        } else {
            $pubcatcount[$cid][$ptid] = $count;
        }
        $result->MoveNext();
    }
    foreach ($pubcatcount as $id1 => $val) {
        $total = 0;
        foreach ($val as $id2 => $count) {
            $total += $count;
        }
        $pubcatcount[$id1]['total'] = $total;
    }

    return $pubcatcount;
}
