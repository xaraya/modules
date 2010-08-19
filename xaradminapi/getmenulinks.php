<?php
/**
 * Return the options for the admin menu
 *
 */

function ckeditor_adminapi_getmenulinks() {

	$menulinks = array();

	if (xarSecurityCheck('AdminCKEditor',0)) {

        $menulinks[] = Array('url'   => xarModURL('ckeditor',
                                                   'admin',
                                                   'modifyconfig'), 
                              'title' => xarML('Modify Configuration'),
                              'label' => xarML('Modify Configuration'));
    }

    if (xarSecurityCheck('AdminCKEditor',0)) {

        $menulinks[] = Array('url'   => xarModURL('ckeditor',
                                                   'admin',
                                                   'overview'), 
                              'title' => xarML('Module Overview'),
                              'label' => xarML('Overview'));
    }

    return $menulinks;
}

?>