<?php

/**
 * File: $Id$
 *
 * Admin view of all pages, in hierarchical format.
 *
 * @package Xaraya
 * @copyright (C) 2004-2010 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 * @todo Support a pager of sorts, or allow display to be limited to specific sub-trees.
 */

function xarpages_admin_viewpages($args)
{
    extract($args);

    // Security check
    if (!xarSecurityCheck('ModerateXarpagesPage', 1, 'Page', 'All')) {
        // No privilege for viewing pages.
        return false;
    }

    // Accept a parameter to allow selection of a single tree.
    xarVarFetch('contains', 'id', $contains, 0, XARVAR_NOT_REQUIRED);

    $data = xarModAPIFunc(
        'xarpages',
        'user',
        'getpagestree',
        array('key' => 'index', 'dd_flag' => false, 'tree_contains_pid' => $contains)
    );

    if (empty($data['pages'])) {
        // TODO: pass to template.
        return $data; //xarML('NO PAGES DEFINED');
    } else {
        $data['pages'] = xarModAPIfunc('xarpages', 'tree', 'array_maptree', $data['pages']);
    }

    $data['contains'] = $contains;

    // Check modify and delete privileges on each page.
    // ModeratePage - allows overview
    // EditPage - allows basic changes, but no moving or renaming (good for sub-editors who manage content)
    // AddPage - new pages can be added (further checks may limit it to certain page types)
    // DeletePage - page can be renamed, moved and deleted
    if (!empty($data['pages'])) {
        foreach ($data['pages'] as $key => $page) {
            if (xarSecurityCheck('ModerateXarpagesPage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
                $data['pages'][$key]['moderate_allowed'] = true;
            }
            if (xarSecurityCheck('EditXarpagesPage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
                $data['pages'][$key]['edit_allowed'] = true;
            }
            if (xarSecurityCheck('DeleteXarpagesPage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
                $data['pages'][$key]['delete_allowed'] = true;
            }
        }
    }

    $pages = array_reverse($data['pages'], true);

    $pagestree = array();

    $parentid = 'x';
    $children = array(0 => array());

    // convert the flat tree structure into a nested one for display
    foreach ($pages as $tmppage) {
        $pid = $tmppage['pid'];
        $parent = $tmppage['parent_pid'];
        $left = $tmppage['left'];
        if ($parent !== $parentid) {
            if (isset($children[$parent]) && count($children[$parent]) > 0) {
                ksort($children[$parent], SORT_NUMERIC);
                $tmppage['children'] = $children[$pid];
            } else {
                $children[$parent] = array();
                if (isset($children[$pid])) {
                    ksort($children[$pid], SORT_NUMERIC);
                    $tmpcat['children'] = $children[$pid];
                }
            }
            $parentid = $parent;
            $children[$parent][$left] = $tmppage;
            ksort($children[$parent], SORT_NUMERIC);
        } else {
            $children[$parent][$left] = $tmppage;
            ksort($children[$parent], SORT_NUMERIC);
        }
        if ($parent == 0) {
            if (isset($children[$pid]) && count($children[$pid]) > 0) {
                ksort($children[$pid], SORT_NUMERIC);
                $tmppage['children'] = $children[$pid];
            }
            $pagestree[$left] = $tmppage;
        }
    }
    ksort($pagestree, SORT_NUMERIC);

    $data['pages'] = $pagestree;

    // Check if the user is allowed to add pages.
    if (xarSecurityCheck('AddXarpagesPage', 0, 'Page', 'All')) {
        $data['add_allowed'] = true;
    }

    return $data;
}
