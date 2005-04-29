<?php

/**
 * Check to see if the current secondary ID (or it's parents)
 * are listed in the interesections list
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_userapi_check_secondary_intersections( $args )
{
    extract($args);

    if (!isset($intersections) || !is_array($intersections) || !count($intersections)) {
        return;
    }

    $current_cids = xarModAPIFunc('navigator', 'user', 'get_current_cats');

    // if we don't have a valid list of cids or a valid tree
    // then return don't display anything....
    if (empty($current_cids) || !count($current_cids)) {
        if (in_array((int) -1, $intersections)) {
            return TRUE;
        } else {
            return FALSE;
        }
    } else {
        extract($current_cids);
    }

    if (in_array($secondary['id'], $intersections)) {
        return TRUE;
    }

    // Use secondary list
    $primary_list = @unserialize(xarModGetVar('navigator', 'categories.list.secondary'));
    xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$primary_list);

    // reindex by cid => metadata
    foreach ($primary_list as $node) {
        $list[$node['cid']] = $node;
    }
    if (!isset($list[$secondary['id']])) {
        return FALSE;
    } else {
        $currentId = $secondary['id'];

        do {
            if (in_array($list[$currentId]['pid'], $intersections)) {
                return TRUE;
            } else {
                $currentId = $list[$currentId]['pid'];
            }
        } while (isset($list[$currentId]));
    }

    return FALSE;
}

?>
