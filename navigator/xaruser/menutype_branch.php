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
 * Display's the List type menu.
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_user_menutype_branch( $args )
{
    if (!xarSecurityCheck('ViewNavigatorMenu', 0, 'Menu', $args['id'], 'navigator')) {
        return;
    }
    
    extract($args);

    if (!isset($exclude) || empty($exclude)) {
        $exclude = array();
    } else {
        $exclude = explode(',', $exclude);
    }

    if (isset($maxdepth) && is_numeric($maxdepth)) {
        $data['maxdepth'] = $maxdepth;
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
            $cids = explode(';',$primary['id']);
            xarVarSetCached('Blocks.articles', 'cids', $cids);
        } else {
            return;
        }
    } else {
        extract($current_cids);
    }

    $curcids[0] = $primary;
    $curcids[1] = NULL;

    if (!empty($rename)) {
        xarModAPIFunc('navigator', 'user', 'set_names',
                       array('curcids' => $curcids,
                             'tree'    => &$tree,
                             'names'   => $rename));
    }

    /*
       Look for the node and, if we don't find it,
       mark each parent category (top most) for exclusion
       leaving only the parent that contains it for display.
    */

    $found = FALSE;
    $search = $primary['id'];
    foreach ($tree as $node) {
        if ($node['cid'] != $search) {
            if (count($node['children'])) {
                if (xarModAPIFUnc('navigator', 'user', 'nested_tree_find_node',
                                    array('tree' => $node['children'],
                                          'search' => $search))) {
                    continue;
                }
            }
            $exclude[] = $node['cid'];
        } else {
            continue;
        }
    }

    if ($emptygroups == 'hide') {
        $testtree = $tree;
        xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$testtree);

        $count_list = xarModAPIFunc('navigator', 'user', 'count_articles_bycat');

        foreach ($testtree as $key => $item) {
            if (isset($count_list[$item['cid']]) && !$item['parent']) {
                $exclude[] = $item['cid'];
            }
        }
    }

    if (!empty($exclude)) {
        // Remove any nodes that need removing
        xarModAPIFunc('navigator', 'user', 'nested_tree_remove_node',
                       array('tree' => &$tree,
                             'cids' => $exclude));
    }

    // Now remove the parent :-)
    reset($tree);
    $list = current($tree);
    $tree = $list['children'];

    if (isset($data['current_primary_id']) && !$data['current_primary_id']) {
         return;
    }

    $navigator_styleSheets = @unserialize(xarModGetVar('navigator', 'style.list.files'));

    if (!is_array($navigator_styleSheets)) {
        $navigator_styleSheets = array();
    }
    
    $navigator_styleName = "navigator-branchmenu";
    if (is_array($navigator_styleSheets) && !in_array($navigator_styleName, $navigator_styleSheets)) {
        $navigator_styleSheets[] = $navigator_styleName;
        xarModSetVar('navigator', 'style.list.files', serialize($navigator_styleSheets));
    }


    $data['primary']  = $primary;
    $data['tree']     = $tree;
    // xarDerefData('$data', $data, TRUE);
    return $data;
}

?>
