<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function uploads_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditUploads')) {
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   'main'),
                              'title' => xarML('Uploads Module Overview'),
                              'label' => xarML('Overview'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   "view"),
                              'title' => xarML('View All Uploads'),
                              'label' => xarML('View Uploads'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'user',
                                                   'uploadform'),
                              'title' => xarML('Upload a File'),
                              'label' => xarML('Upload File'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   'fileimport'),
                              'title' => xarML('Import Files'),
                              'label' => xarML('Import Files'));
    }
    if (xarSecurityCheck('AdminUploads')) {
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Edit the Uploads Configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>