<?php
/**
 * utility function pass individual menu items to the main menu
 *
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
                             'title' => xarML('View All Files'),
                             'label' => xarML('View Files'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'get_files'),
                             'title' => xarML('Add a File'),
                             'label' => xarML('Add File'));
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
