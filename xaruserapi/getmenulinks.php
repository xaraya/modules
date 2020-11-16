<?php
/**
 * Return the options for the user menu
 *
 */

function xarayatesting_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurity::check('ViewXarayatesting', 0)) {
        $menulinks[] = array('url'   => xarController::URL(
            'xarayatesting',
            'user',
            'main'
        ),
                              'title' => xarML('An overview page for this module'),
                              'label' => xarML('Overview'));
        $menulinks[] = array('url'   => xarController::URL(
            'xarayatesting',
            'user',
            'view'
        ),
                              'title' => xarML('Display the site test suites'),
                              'label' => xarML('Site Tests'));
        $menulinks[] = array('url'   => xarController::URL(
            'xarayatesting',
            'user',
            'testpage'
        ),
                              'title' => xarML('Run the test suites'),
                              'label' => xarML('Run Xaraya Tests'));
    }

    return $menulinks;
}
