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

    foreach ($tree as $key => $node) {
        if ($node['cid'] != $search) {
            if (count($node['children'])) {

                $curr_found = xarModAPIFUnc('navigator', 'user', 'nested_tree_find_node',
                                             array('tree' => &$tree[$key]['children'],
                                                   'search' => $search));
                if ($curr_found) {
                    $tree[$key]['trail'] = 1;
                    $args['tree'] = $tree;                    
                    return TRUE;
                } 
            } 
        } else {
            $tree[$key]['trail'] = 1;
            $args['tree'] = $tree;
            return TRUE;
        }
    }
    
    return FALSE; 
}


?>
