<?php

/**
 * View the pages in an hierarchical format.
 * TODO: Allow individual trees to be displayed and for trees to
 * be cut off at an arbitrary level when viewing all pages.
 */

function xarpages_admin_viewpages()
{
    // Security check
    if (!xarSecurityCheck('ModeratePage', 1, 'Page', 'All')) {
        // No privilege for viewing pages.
        return false;
    }

    $data = xarModAPIFunc(
        'xarpages', 'user', 'getpagestree',
        array('key' => 'index', 'dd_flag' => false)
    );

    if (empty($data['pages'])) {
        // TODO: pass to template.
        return xarML('NO PAGES DEFINED');
    } else {
        $data['pages'] = xarModAPIfunc('xarpages', 'tree', 'array_maptree', $data['pages']);
    }

    // Check modify and delete privileges on each page.
    // ModeratePage - allows overview
    // EditPage - allows basic changes, but no moving or renaming (good for sub-editors who manage content)
    // AddPage - new pages can be added (further checks may limit it to certain page types)
    // DeletePage - page can be renamed, moved and deleted
    if (!empty($data['pages'])) {
        foreach($data['pages'] as $key => $page) {
            if (xarSecurityCheck('ModeratePage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
                $data['pages'][$key]['moderate_allowed'] = true;
            }
            if (xarSecurityCheck('EditPage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
                $data['pages'][$key]['edit_allowed'] = true;
            }
            if (xarSecurityCheck('DeletePage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
                $data['pages'][$key]['delete_allowed'] = true;
            }
        }
    }

    // Check if the user is allowed to add pages.
    if (xarSecurityCheck('AddPage', 0, 'Page', 'All')) {
        $data['add_allowed'] = true;
    }

    return $data;
}

?>