<?php

/**
 * View page types.
 * TODO: replace all this stuff with a generic hierarchy module.
 * It is presently held together with rubber bands and sticky tape.
 */

function xarpages_admin_viewtypes()
{
    // Security check
    //if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
    //    // No privilege for editing survey structures.
    //    return false;
    //}

    $types = xarModAPIFunc(
        'xarpages', 'user', 'gettypes',
        array('key' => 'index', 'dd_flag' => false)
    );

    if (empty($types)) {
        // TODO: pass to template.
        return 'NO PAGES TYPES DEFINED';
    }

    return array('types' => $types);
}

?>