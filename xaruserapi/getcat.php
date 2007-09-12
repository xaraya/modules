<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * get categories
 *
 * @param int $args['cid'] restrict output only to this category ID and its sibbling (default none)
 * @param int $args['eid'] do not output this category and its sibblings (default none)
 * @param int $args['maximum_depth'] return categories with the given depth or less
 * @param int $args['minimum_depth'] return categories with the given depth or more
 * @param $args['indexby'] =string= specify the index type for the result array (default 'default')
 *  They only change the output IF 'cid' is set:
 *    @param $args['getchildren'] =Boolean= get children of category (default false)
 *    @param $args['getparents'] =Boolean= get parents of category (default false)
 *    @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @return array Array of categories, or =Boolean= false on failure

 * Examples:
 *    getcat() => Return all the categories
 *    getcat(Array('cid' -> ID)) => Only cid and its children, grandchildren and
 *                                   every other sibbling will be returned
 *    getcat(Array('eid' -> ID)) => All categories will be returned EXCEPT
 *                                   eid and its children, grandchildren and
 *                                   every other sibbling will be returned
 */
function categories_userapi_getcat($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set some defaults.
    if (!isset($return_itself)) $return_itself = false;
    if (empty($indexby)) $indexby = 'default';
    if (!isset($getchildren)) $getchildren = false;
    if (!isset($getparents)) $getparents = false;

    if (!isset($start)) {
        $start = 0;
    } elseif (!is_numeric($start)) {
        return false;
    } else {
        // The pager starts counting from 1
        // SelectLimit starts from 0
        $start--;
    }

    if (!isset($count)) {
        $count = 0;
    } elseif (!is_numeric($count)) {
        return false;
    }

    $categoriestable = $xartable['categories'];
    $bindvars = array();
    $SQLquery = 'SELECT'
        . ' COUNT(p2.xar_cid) AS indent,'
        . ' p1.xar_cid, p1.xar_name, p1.xar_description, p1.xar_image, p1.xar_parent, p1.xar_left, p1.xar_right'
        . ' FROM ' . $categoriestable . ' AS p1'
        . ' INNER JOIN ' . $categoriestable . ' AS p2'
        . ' ON p1.xar_left >= p2.xar_left AND p1.xar_left <= p2.xar_right';

    // WHERE-clauses, all to be joined using AND
    $where = array();

    if (isset($cid) && !is_array($cid) && $cid != false) {
        if ($getchildren || $getparents) {
            $where1 = array();

            // We have the category ID but we need
            // to know its left and right values
            $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $cid));
            if ($cat == false) return array();

            // If not returning itself we need to take the appropriate left values
            if ($return_itself) {
                $return_child_left = $cat['left'];
                $return_parent_left = $cat['left'];
            } else {
                $return_child_left = $cat['left'] + 1;
                $return_parent_left = $cat['left'] - 1;
            }

            if ($getchildren) {
                $where1[] = '(p1.xar_left >= ? AND p1.xar_left <= ?)';
                $bindvars[] = (int)$return_child_left;
                $bindvars[] = (int)$cat['right'];
            }

            if ($getparents) {
                $where1[] = '(? >= p1.xar_left AND ? <= p1.xar_right)';
                // Same value bound twice.
                $bindvars[] = (int)$return_parent_left;
                $bindvars[] = (int)$return_parent_left;
            }

            $where[] = '(' . implode(' OR ', $where1) . ')';
        } else {
            // !(isset($getchildren)) && !(isset($getparents))
            // Return ONLY the info about the category with the given CID
            $where[] = 'p1.xar_cid = ?';
            $bindvars[] = (int)$cid;
        }
    }

    if (isset($eid) && !is_array($eid) && $eid != false) {
        $ecat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $eid));
        if ($ecat == false) return array();

        // Equivalent to NOT BETWEEN
        $where[] = '(p1.xar_left < ? OR p1.xar_left > ?)';
        $bindvars[] = (int)$ecat['left'];
        $bindvars[] = (int)$ecat['right'];
    }

    if (!empty($where)) $SQLquery .= ' WHERE ' . implode(' AND ', $where);

    // Have to specify all selected attributes in GROUP BY
    $SQLquery .= ' GROUP BY p1.xar_cid, p1.xar_name, p1.xar_description, p1.xar_image, p1.xar_parent, p1.xar_left, p1.xar_right ';

    $having = array();

    // Bug #620: Postgres doesn't support column aliases in HAVING or ORDER BY clauses
    if (isset($minimum_depth) && is_numeric($minimum_depth)) {
        $having[] = 'COUNT(p2.xar_cid) >= ?';
        $bindvars[] = $minimum_depth;
    }
    if (isset($maximum_depth) && is_numeric($maximum_depth)) {
        $having[] = 'COUNT(p2.xar_cid) < ?';
        $bindvars[] = $maximum_depth;
    }
    if (count($having) > 0) {
        $SQLquery .= ' HAVING ' . join(' AND ', $having);
    }

    $SQLquery .= ' ORDER BY p1.xar_left';

    // cfr. xarcachemanager - this approach might change later
    $expire = xarModGetVar('categories', 'cache.userapi.getcat');
    if (is_numeric($count) && $count > 0 && is_numeric($start) && $start > -1) {
        if (!empty($expire)){
            $result = $dbconn->CacheSelectLimit($expire, $SQLquery, $count, $start, $bindvars);
        } else {
            $result = $dbconn->SelectLimit($SQLquery, $count, $start, $bindvars);
        }
    } else {
        if (!empty($expire)){
            $result = $dbconn->CacheExecute($expire, $SQLquery, $bindvars);
        } else {
            $result = $dbconn->Execute($SQLquery, $bindvars);
        }
    }

    if (!$result) return;

    if ($result->EOF) {
        // No category found
        return array();
    }

    $categories = array();

    $index = -1;
    while (!$result->EOF) {
        list($indentation, $cid, $name, $description, $image, $parent, $left, $right) = $result->fields;
        $result->MoveNext();

        // If no privileges to view, then skip this category.
        if (!xarSecurityCheck('ViewCategories', 0, 'Category', "$name:$cid")) continue;

        if ($indexby == 'cid') {
            $index = $cid;
        } else {
            $index++;
        }

        $categories[$index] = compact('indentation', 'cid', 'name', 'description', 'image', 'parent', 'left', 'right');
    }
    $result->Close();

    return $categories;
}

?>