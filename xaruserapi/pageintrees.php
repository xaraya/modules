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

function publications_userapi_pageintrees($args)
{
    extract($args);

    if (!isset($pid) || !is_numeric($pid) || !isset($tree_roots) || !is_array($tree_roots)) {
        return false;
    }

    $xartable =& xarDB::getTables();
    $dbconn = xarDB::getConn();

    // For the page to be somewhere in a tree, identified by the root of that tree,
    // it's xar_left column must be between the xar_left and xar_right columns
    // of the tree root.
    $query = 'SELECT COUNT(*)'
        . ' FROM ' . $xartable['publications'] . ' AS testpage'
        . ' INNER JOIN ' . $xartable['publications'] . ' AS testtrees'
        . ' ON testpage.leftpage_id BETWEEN testtrees.leftpage_id AND testtrees.rightpage_id'
        . ' AND testtrees.id IN (?' .str_repeat(',?', count($tree_roots)-1). ')'
        . ' WHERE testpage.id = ?';

    // Add the pid onto the tree roots to form the full bind variable set.
    $tree_roots[] = $pid;
    $result = $dbconn->execute($query, $tree_roots);

    if (!$result || $result->EOF) {
        return false;
    }

    list($count) = $result->fields;

    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
