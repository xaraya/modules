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
    $matrix    = xarModGetVar('navigator', 'style.matrix') ? TRUE : FALSE;
    $primary   = 'All';
    $secondary = 'All';
    if ($matrix) {
        
        if (isset($args['type']) && eregi('(primary|secondary)', $args['type'])) {
            $type = strtolower($args['type']);
            
            $current_ids = xarModAPIFunc('navigator', 'user', 'get_current_cats');
            if (count($current_ids)) {
                $primary   = $current_ids['primary']['id'];
                $secondary = $current_ids['secondary']['id'];
            }
        } else {
            $type = 'primary';
        }
    } else {
        $type = 'primary';
    }
    

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
             
        if ('primary' == $type) {
            $instance = $branch['cid'] . ':' . $secondary;
        } else {
            $instance = $primary . ':' . $branch['cid'];
        }

        if (!xarSecurityCheck('ViewNavigatorMenuItem', 0, 'Menu Item', $instance, 'navigator')) {
            unset($tree[$key]);
            continue;
        }

        if (in_array($branch['cid'], $cids)) {
            if ($keep_parent) {
                unset($tree[$key]['children']);
            } else {
                unset($tree[$key]);
            }
        } else {
            if (isset($branch['children']) && count($branch['children'])) {
                navigator_userapi_nested_tree_remove_node(array('cids' => $cids,
                                                                'tree' => &$tree[$key]['children'],
                                                                'type' => $type,
                                                                'keep-parent' => $keep_parent));
            }
        }
    }
}


?>