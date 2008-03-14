<?php

/*
 * Determines whether a given page is under one or
 * more of a collection of page trees. If a page
 * must lie within a selection of trees, then this
 * boolean function will test that.
 * @param pid integer The page ID that must lie within the the trees
 * @param tree_roots array of integer The roots of the trees to test for
 * @returns boolean true if the page 'pid' lies within any of the trees 'tree_roots'
 * @todo This could probably be moved to the 'treeapi' since it is generic tree-related
 */

function xarpages_userapi_pageintrees($args)
{
    extract($args);

    if (!isset($pid) || !is_numeric($pid) || !isset($tree_roots) || !is_array($tree_roots)) {
        return false;
    }

    $xartable = xarDB::getTables();
    $dbconn = xarDB::getConn();

    // For the page to be somewhere in a tree, identified by the root of that tree,
    // it's xar_left column must be between the xar_left and xar_right columns
    // of the tree root.
    $query = 'SELECT COUNT(*)'
        . ' FROM ' . $xartable['xarpages_pages'] . ' AS testpage'
        . ' INNER JOIN ' . $xartable['xarpages_pages'] . ' AS testtrees'
        . ' ON testpage.xar_left BETWEEN testtrees.xar_left AND testtrees.xar_right'
        . ' AND testtrees.xar_pid IN (?' .str_repeat(',?', count($tree_roots)-1). ')'
        . ' WHERE testpage.xar_pid = ?';

    // Add the pid onto the tree roots to form the full bind variable set.
    $tree_roots[] = $pid;
    $result = $dbconn->execute($query, $tree_roots);

    if (!$result || $result->EOF) {return false;}

    list($count) = $result->fields;

    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}

?>
