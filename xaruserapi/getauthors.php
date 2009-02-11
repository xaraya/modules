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
 * get a list of article authors depending on additional module criteria
 *
 * @param $args['cids'] array of cids that we are counting for (OR/AND)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 *
 * @param $args['owner'] the ID of the author
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['startdate'] publications published at startdate or later
 *                           (unix timestamp format)
 * @param $args['enddate'] publications published before enddate
 *                         (unix timestamp format)
 * @return array of author id => author name
 */
function publications_userapi_getauthors($args)
{
    // Database information
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from publications
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the publications-specific columns too now
    $publicationsdef = xarModAPIFunc('publications','user','leftjoin',$args);

    // Load API
    if (!xarModAPILoad('roles', 'user')) return;

    // Get the field names and LEFT JOIN ... ON ... parts from users
    $usersdef = xarModAPIFunc('roles','user','leftjoin');

// TODO: make sure this is SQL standard
    // Start building the query
    $query = 'SELECT DISTINCT ' . $publicationsdef['owner'] . ', ' . $usersdef['name'];
    $query .= ' FROM ' . $publicationsdef['table'];

    // Add the LEFT JOIN ... ON ... parts from users
    $query .= ' LEFT JOIN ' . $usersdef['table'];
    $query .= ' ON ' . $usersdef['field'] . ' = ' . $publicationsdef['owner'];

    if (!isset($args['cids'])) {
        $args['cids'] = array();
    }
    if (!isset($args['andcids'])) {
        $args['andcids'] = false;
    }
    if (count($args['cids']) > 0) {
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

    // Order by author name
    $query .= ' ORDER BY ' . $usersdef['name'] . ' ASC';

    // Run the query - finally :-)
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $authors = array();
    while (!$result->EOF) {
        list($uid, $name) = $result->fields;
        $authors[$uid] = array('id' => $uid, 'name' => $name);
        $result->MoveNext();
    }

    $result->Close();

    return $authors;
}

?>
