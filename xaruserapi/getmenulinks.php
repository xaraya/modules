<?php
/**
 * Return the options for the user menu
 *
 */

function ckeditor_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewCKEditor',0)) {
        $menulinks[] = array('url'   => xarModURL('ckeditor',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}

?>
