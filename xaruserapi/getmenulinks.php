<?php
/**
 * Return the options for the user menu
 *
 */

function karma_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewKarma',0)) {
        $menulinks[] = array('url'   => xarModURL('karma',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}

?>
