<?php

/**
 * Find and remove all specified nodes. If keep-parent = TRUE
 * only remove the node's children and not the node itself.
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_userapi_nested_tree_remove_node( $args )
{
    if (isset($args['keep-parent']) && $args['keep-parent'] == TRUE) {
        $keep_parent = TRUE;
    } else {
        $keep_parent = FALSE;
    }

    if (!isset($args['cids']) || empty($args['cids'])) {
        return;
    } else {
        if (is_array($args['cids'])) {
            $cids = $args['cids'];
        } else {
            $cids = array($args['cids']);
        }
    }

    if (!isset($args['tree']) || empty($args['tree'])) {
        return;
    } else {
        $tree = &$args['tree'];
    }

    foreach ($tree as $key => $branch) {
        if (in_array($branch['cid'], $cids)) {
            if ($keep_parent) {
                unset($tree[$key]['children']);
            } else {
                unset($tree[$key]);
            }
        } else {
            if (isset($branch['children']) && count($branch['children'])) {
                navigator_userapi_nested_tree_remove_node(array('cids' => $cids,
                                                          'tree' => &$tree[$key]['children']));
            }
        }
    }
}


?>