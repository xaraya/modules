<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * count number of items depending on additional module criteria
 *
 * @param $args['catid'] string of category id(s) that we're counting in, or
 * @param $args['cids'] array of cids that we are counting in (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 *
 * @param $args['owner'] the ID of the author
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['startdate'] publications published at startdate or later
 *                           (unix timestamp format)
 * @param $args['enddate'] publications published before enddate
 *                         (unix timestamp format)
 * @return int number of items
 */
function publications_userapi_countitems($args)
{
    // Database information
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from publications
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the publications-specific columns too now
    $publicationsdef = xarModAPIFunc('publications','user','leftjoin',$args);

// TODO: make sure this is SQL standard
    // Start building the query
    if($dbconn->databaseType == 'sqlite') {
        $query = 'SELECT COUNT(*)
                  FROM ( SELECT DISTINCT '. $publicationsdef['field'].'
                         FROM '. $publicationsdef['table']; // WATCH OUT, UNBALANCED
    } else {
        $query = 'SELECT COUNT(DISTINCT ' . $publicationsdef['field'] . ')';
        $query .= ' FROM ' . $publicationsdef['table'];
    }

    if (!isset($args['cids'])) {
        $args['cids'] = array();
    }
    if (!isset($args['andcids'])) {
        $args['andcids'] = false;
    }
    if (count($args['cids']) > 0 || !empty($args['catid'])) {
        // Load API
        if (!xarModAPILoad('categories', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
        $args['modid'] = xarModGetIDFromName('publications');
        if (isset($args['ptid']) && !isset($args['itemtype'])) {
            $args['itemtype'] = $args['ptid'];
        }
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',$args);

        $query .= ' LEFT JOIN ' . $categoriesdef['table'];
        $query .= ' ON ' . $categoriesdef['field'] . ' = '
                . $publicationsdef['id'];
        $query .= $categoriesdef['more'];
        $docid = 1;
    }

    // Create the WHERE part
    $where = array();
    // we rely on leftjoin() to create the necessary publications clauses now
    if (!empty($publicationsdef['where'])) {
        $where[] = $publicationsdef['where'];
    }
    if (!empty($docid)) {
        // we rely on leftjoin() to create the necessary categories clauses
        $where[] = $categoriesdef['where'];
    }
    if (count($where) > 0) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    // Balance parentheses
    if($dbconn->databaseType == 'sqlite') $query .=')';
    // Run the query - finally :-)
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->EOF) {
        return;
    }

    $num = $result->fields[0];
    $result->Close();

    return $num;
}

?>
