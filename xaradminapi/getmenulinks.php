<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function images_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminImages')) {
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'main'),
                             'title' => xarML('Images Module Overview'),
                             'label' => xarML('Overview'));
        if (xarModIsAvailable('uploads') && xarSecurityCheck('AdminUploads',0)) {
            $menulinks[] = Array('url'   => xarModURL('images',
                                                      'admin',
                                                      'uploads'),
                                 'title' => xarML('View Uploaded Images'),
                                 'label' => xarML('View Uploaded Images'));
        }
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'derivatives'),
                             'title' => xarML('View Derivative Images'),
                             'label' => xarML('View Derivative Images'));
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'browse'),
                             'title' => xarML('Browse Server Images'),
                             'label' => xarML('Browse Server Images'));
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'phpthumb'),
                             'title' => xarML('Define Settings for Image Processing'),
                             'label' => xarML('Image Processing'));
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Edit the Images Configuration'),
                             'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>
