<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function filemanager_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditFileManager')) {
        $menulinks[] = Array('url'   => xarModURL('filemanager',
                                                  'admin',
                                                  'main'),
                             'title' => xarML('FileManager Module Overview'),
                             'label' => xarML('Overview'));
        $menulinks[] = Array('url'   => xarModURL('filemanager',
                                                  'admin',
                                                  "view"),
                             'title' => xarML('View All Files'),
                             'label' => xarML('View Files'));
        $menulinks[] = Array('url'   => xarModURL('filemanager',
                                                  'admin',
                                                  'get_files'),
                             'title' => xarML('Add a File'),
                             'label' => xarML('Add File'));
    }
    if (xarSecurityCheck('AdminFileManager')) {
        $menulinks[] = Array('url'   => xarModURL('filemanager',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Edit the FileManager Configuration'),
                             'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>