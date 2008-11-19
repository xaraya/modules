<?php
/**
 * Return the options for the user menu
 *
 */

function foo_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewFoo',0)) {
        $menulinks[] = array('url'   => xarModURL('foo',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}

?>
