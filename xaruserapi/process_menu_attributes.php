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
 * Processes all the menu attributes, returning the the fully
 * (post)processed tree as well the attribute information itself.
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_userapi_process_menu_attributes( $args )
{
    extract($args);

    if (!isset($base)) {
        // CHECKME: set an exception if no base?
        return;
    }

    if (isset($oneblock) && (strtoupper($oneblock) == 'TRUE' || strtoupper($oneblock) == 'YES')) {
        $oneblock = TRUE;
    } else {
        $oneblock = FALSE;
    }

    if (!isset($exclude) || empty($exclude)) {
        $exclude = array();
    } else {
        $exclude = explode(',', $exclude);
    }

    if (isset($maxdepth) && is_numeric($maxdepth)) {
        $data['maxdepth'] = $maxdepth;
    }


    if (isset($intersects) && !empty($intersects)) {

        if ('none' == $intersects) {
            $intersects = (int) -1;
        }

        $intersects = xarModAPIFunc('navigator', 'user',
                                    'check_current_intersections',
                                     array('intersections' =>
                                                explode(',', $intersects)));

        // Have to make sure we check that it is boolean and and not null
        if ($intersects === FALSE) {
            return '';
        }
    }

    if (isset($rename)) {
        $renameList = xarModAPIFunc('navigator', 'user', 'parse_name_changes',
                                     array('base' => $base, 'names' => $rename));
        $rename = $renameList;
        unset($renameList);
    } else {
        $rename = array();
    }

    if (!isset($emptygroups) || empty($emptygroups)) {
        $emptygroups = 'show';
    }

    $data['emptygroups'] = $emptygroups;

    $tree = @unserialize(xarModGetVar('navigator', 'categories.list.'.$base));
    $current_cids = xarModAPIFunc('navigator', 'user', 'get_current_cats');

    if (xarModGetVar('navigator', 'style.matrix')) {
        $matrix = TRUE;
    } else {
        $matrix = FALSE;
    }

    // if we don't have a valid list of cids or a valid tree
    // then return don't display anything....
    if (empty($current_cids) || empty($tree)) {
        if (!empty($current_cids) && empty($tree)) {
            if (!isset($current_cids[$base])) {
                echo "<br />Tag's base attribute does ";
                echo "not match configuration base.<br />";
                echo "<br />Configuration base: " . key($current_cids);
                echo "<br />Tag attribute base: $base";
            }
        } elseif (!empty($tree) && empty($current_cids)) {
            // Otherwise,
            $primary['id'] = 0;
            $primary['name'] = '';

            if ($matrix) {
                $secDef = xarModGetVar('navigator', 'categories.secondary.default');
                if (!isset($secDef) || empty($secDef)) {
                    $secondary['id'] = 0;
                } else {
                    $secondary['id'] = $secDef;
                }
                $secondary['name'] = 'Home';
                $cids = explode(';',$primary['id'] . ';' . $secondary['id']);
            } else {
                $cids = explode(';',$primary['id']);
            }
            xarVarSetCached('Blocks.articles', 'cids', $cids);
        } else {
            return;
        }
    } else {
        extract($current_cids);
    }

    if ($matrix) {
        $secDef = xarModGetVar('navigator', 'categories.secondary.default');
        xarModAPIFunc('navigator', 'user', 'set_startpoint',
                       array('tree' => &$tree, 'startpoint' => $secDef));
        $data['current_primary_id'] = $primary['id'];
        $data['current_cid'] = $data['current_secondary_id'] = $secondary['id'];
        $data['secDef'] = $secDef;

    } else {
        $data['current_cid'] = $data['current_primary_id'] = $primary['id'];
        $data['current_secondary_id'] = 0;
    }

    if (!empty($exclude)) {
        // Remove any nodes that need removing
        xarModAPIFunc('navigator', 'user', 'nested_tree_remove_node',
                       array('tree' => &$tree,
                             'cids' => $exclude,
                             'type' => $base));
    }

    $curcids[0] = $primary;
    $curcids[1] = isset($secondary) ? $secondary : NULL;

    if (!empty($rename)) {
        xarModAPIFunc('navigator', 'user', 'set_names',
                       array('curcids' => $curcids,
                             'tree'    => &$tree,
                             'names'   => $rename));
    }

    xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$tree);

    if ($emptygroups == 'hide') {
        $count_list = xarModAPIFunc('navigator', 'user', 'count_articles_bycat');
        foreach ($tree as $key => $item) {
            if (isset($count_list[$item['cid']])) {
                $tree[$key]['total'] = $count_list[$item['cid']];
            } else {
                $tree[$key]['total'] = 0;
            }
        }
    }

    if ($oneblock == TRUE) {
        $first = TRUE;
        foreach ($tree as $key => $item) {
            if ($first) {
                $first = FALSE;
                continue;
            } else {
                if ($item['parent'] && $item['indent'] == 0) {
                    unset($tree[$key]);
                }
            }
        }
    }

    $data['matrix']   = $matrix;
    $data['primary']  = $primary;
    $data['tree']     = $tree;

    return $data;
}

?>
