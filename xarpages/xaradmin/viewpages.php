<?php

/**
 * View the pages in an hierarchical format.
 * TODO: Allow individual trees to be displayed and for trees to
 * be cut off at an arbitrary level when viewing all pages.
 */

function xarpages_admin_viewpages()
{
    // Security check
    //if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
    //    // No privilege for editing survey structures.
    //    return false;
    //}

    $pages = xarModAPIFunc(
        'xarpages', 'user', 'getpagestree',
        array('key' => 'index', 'dd_flag' => false)
    );

    if (empty($pages)) {
        // TODO: pass to template.
        return 'NO PAGES DEFINED';
    } else {
        $pages['pages'] = xarModAPIfunc('xarpages', 'tree', 'array_maptree', $pages['pages']);
    }

    return $pages;
}

?>