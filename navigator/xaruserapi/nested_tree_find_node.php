<?php
/*
 * File: $Id: $
 *
 * @package Navigator
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author "Carl P. Corliss" <ccorliss@schwabfoundation.org>
*/

/**
 * Recursively search for a specific cid
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_userapi_nested_tree_find_node( $args )
{
    extract ($args);

    if ((!isset($children) || !is_array($children)) && !isset($search)) {
        return FALSE;
    }

    $found = FALSE;

    foreach ($tree as $node) {
        if ($node['cid'] != $search) {
            if (count($node['children'])) {
                if (xarModAPIFUnc('navigator', 'user', 'nested_tree_find_node',
                                    array('tree' => $node['children'],
                                          'search' => $search))) {
                    return TRUE;
                }
            }
        } else {
            return TRUE;
        }
    }

    return FALSE;
}


?>
