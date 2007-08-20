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
 * get info on a specific (list of) category
 *
 * @param int   $args['cid'] id of category to get info, or
 * @param array $args['cids'] array of category ids to get info
 * @return array Category info array, or array of cat info arrays, false on failure
 * @todo Fetch dynamic data for the categories where hooks are in place
 */
function categories_userapi_getcatinfo($args)
{
    extract($args);

    // Field names (for the database and return element keys).
    static $s_fields = array('cid', 'name', 'description', 'image', 'parent', 'left', 'right');

    // Cache categories as we fetch them.
    // TODO: when categories is one big class, then cacheing can be shared between all APIs.
    static $s_cache = array();

    // User function for setting all elements of an array NULL
    static $s_func_null_array = NULL;

    if (!isset($s_func_null_array)) $s_func_null_array = create_function('&$a', '$a = NULL;');

    // TODO: additional validation - cid should be an ID and cids an array of IDs.
    if (!isset($cid) && !isset($cids)) {
        return false;
    }

    // If a single category, then return the cached value.
    if (!empty($cid) && isset($s_cache[$cid])) return $s_cache[$cid];

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $categoriestable = $xartable['categories'];

    $SQLquery = 'SELECT xar_cid, xar_name, xar_description, xar_image, xar_parent, xar_left, xar_right'
        . ' FROM ' . $categoriestable;
    if (isset($cid)) {
        $SQLquery .= ' WHERE xar_cid = ?';
        $bindvars = array((int)$cid);
    } else {
        // Remove any cached categories from the query.
        // This may (with luck) result in nothing to query at all.
        // Start by creating a placeholder array(cid1 => NULL, cid2 => NULL, etc).
        $info = array_flip($cids);
        array_walk($info, $s_func_null_array);

        foreach($cids as $ckey => $ccid) {
            if (isset($s_cache[$ccid])) {
                $info[$ccid] = $s_cache[$ccid];
                unset($cids[$ckey]);
            }
        }

        // If all required categories were cached then return the cached array now.
        if (count($cids) == 0) return $info;

        $SQLquery .= ' WHERE xar_cid IN (?' . str_repeat(',?', count($cids)-1) . ')';
        $bindvars = $cids;
    }

    $result = $dbconn->Execute($SQLquery, $bindvars);
    if (!$result) return;

    if (isset($cid)) {
        // Return if no category found.
        if ($result->EOF) return false;

        list($cid, $name, $description, $image, $parent, $left, $right) = $result->fields;

        $cid = (int)$cid;
        $parent = (int)$parent;
        $left = (int)$left;
        $right = (int)$right;

        $info = compact($s_fields);

        // Cache the category if not already.
        if (empty($s_cache[$cid])) $s_cache[$cid] = $info;
    } else {
        // Even if no rows were found, we want to continue here because the
        // info array may already populated with cached categories.
        while (!$result->EOF) {
            list($cid, $name, $description, $image, $parent, $left, $right) = $result->fields;

            $cid = (int)$cid;
            $parent = (int)$parent;
            $left = (int)$left;
            $right = (int)$right;

            $info[$cid] = compact($s_fields);

            // Cache the category if not already.
            if (empty($s_cache[$cid])) $s_cache[$cid] = $info[$cid];

            $result->MoveNext();
        }

        // Remove any NULL value categories from the results.
        // These will be categories that didn't exist in the database.
        $info = array_filter($info);
    }

    return $info;
}

?>