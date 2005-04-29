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
 * Recursively set the total articles each pri+sec cat has
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_userapi_nested_tree_hide_emptygroups( $args )
{
    $reInit     = FALSE;
    $tree       = array();
    $countList  = array();
    extract ($args);
    
    if (!isset($args['tree']) || empty($args['tree'])) {
        die("damn - no tree...");
        return;
    } else {
        $tree =& $args['tree'];
    }
    
    if (empty($countList)) {
        foreach ($tree as $pkey => $node) {
            $countList[$node['cid']] = xarModAPIFunc('navigator', 'user', 'count_articles_bycat', array('pkeyId' => $node['cid']));
            $tree[$pkey]['total'] = 1;
        }
    }

    $hasChildWithArticle = FALSE;
    foreach ($tree as $key => $node) {
        if (isset($node['children']) && count($node['children'])) {
            $childTotal = navigator_userapi_nested_tree_hide_emptygroups(array('tree' => &$tree[$key]['children'], 'countList' => $countList));
        } else {
            $childTotal = 0;
        }
        if (!isset($node['total'])) {
            if (isset($countList[$node['primary']][$node['cid']])) {
                $tree[$key]['total'] = $countList[$node['primary']][$node['cid']];
            } elseif ($childTotal > 0) {
                $tree[$key]['total'] = 1;
            } else {
                $tree[$key]['total'] = 0;
            }
        }
        
        if ($tree[$key]['total'] > 0) {
            $hasChildWithArticle = TRUE;
        }
    }
    
    return $hasChildWithArticle; 
}


