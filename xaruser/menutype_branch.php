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
    $args['base'] = $base = 'secondary';
    $data = xarModAPIFunc('navigator', 'user', 'process_menu_attributes', $args);

    if (!isset($data) || empty($data)) {
        return;
    } else {
        extract($data);
    }

    if (isset($id) && trim($id)) {
        $search = '[^A-Za-z0-9._-]';
        $replace = '_';
        $templateName = strtolower(preg_replace($search, $replace, $id));
    }
    $priTree = @unserialize(xarModGetVar('navigator', 'categories.list.primary'));
    $secTree = @unserialize(xarModGetVar('navigator', 'categories.list.secondary'));
    if (isset($tree)) $tree =array();
    
    if ($matrix) {
        if (isset($intersections)) {
            $isPrimarySet = FALSE;
            foreach ($intersections as $catId) {
                foreach ($priTree as $key => $node) {
                    if ($catId == $node['cid']) {
                        if (count($intersections) == 1 && !$isPrimarySet) {
                            $isPrimarySet = TRUE;
                            $primary['id']   = $node['cid'];
                            $primary['name'] = $node['name'];
                            $data['current_primary_id'] = $primary['id'];
                        }
                        $tree[$key] = $priTree[$key];
                        break;
                    }
                }
                $clone = $secTree;
                xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$clone);
                foreach ($clone as $k => $v) {
                    $clone[$k]['primary'] = $catId;
                }
                xarModAPIFunc('navigator', 'user', 'nested_tree_create', array('tree' => &$clone));

                $tree[$key]['children'] = $clone;
            }
        }
    }
    $curcids[0] = $primary;
    $curcids[1] = NULL;
    if (!empty($exclude)) {
        $_cids = array('primary' => &$primary);
        // Remove any nodes that need removing
        xarModAPIFunc('navigator', 'user', 'nested_tree_remove_node',
                       array('tree' => &$tree,
                             'cids' => $exclude,
                             'type' => 'secondary',
                             'curcids' => $_cids));
    }

    /*
       Look for the node and, if we don't find it,
       mark each parent category (top most) for exclusion
       leaving only the parent that contains it for display.
    */
    
    $found = FALSE;
    $search = (isset($secondary['id']) ? $secondary['id'] : $current_secondary_id);
    foreach ($tree as $key => $node) {
        if ($node['cid'] != $search && $search != 0) {
            if (count($node['children']) && !$found) {
                if (xarModAPIFUnc('navigator', 'user', 'nested_tree_find_node',
                                    array('tree' => &$tree[$key]['children'],
                                          'search' => $search))) {
                    $found = TRUE;
                    $tree[$key]['trail'] = TRUE;
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

    $navigator_styleSheets = @unserialize(xarModGetVar('navigator', 'style.list.files'));

    if (!is_array($navigator_styleSheets)) {
        $navigator_styleSheets = array();
    }

    $navigator_styleName = "navigator-branchmenu";
    if (isset($templateName) && !empty($templateName)) {
        $templateFile = xarTplGetThemeDir() . '/modules/navigator/xarstyles/navigator-branchmenu_' . $templateName . '.css';
        if (file_exists($templateFile)) {
            $navigator_styleName .= '_' . $templateName;
        }
    }
            
    if (is_array($navigator_styleSheets) && !in_array($navigator_styleName, $navigator_styleSheets)) {
        $navigator_styleSheets[] = $navigator_styleName;
        xarModSetVar('navigator', 'style.list.files', serialize($navigator_styleSheets));
    }

    if (isset($parents) && strtolower($parents) == 'hide') {
        reset($tree);
        $tree = current($tree);
        $tree = $tree['children'];
        $data['parents'] = 'hide';
    } else {
        $data['parents'] = 'show';
    }
    
    unset($data['tree']);
    $data['primary']  = $primary;
    $data['tree']     = $tree;
    
    return $data;
}

?>
