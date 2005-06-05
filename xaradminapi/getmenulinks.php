<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function comments_adminapi_getmenulinks()
{
    $menulinks[] = Array('url'   => xarModURL('comments',
                                              'admin',
                                              'main'),
                          'title' => xarML('An Overview of the Comments Module'),
                          'label' => xarML('Overview'));

    $menulinks[] = Array('url'   => xarModURL('comments',
                                              'admin',
                                              'stats'),
                         'title' => xarML('View comments per module statistics'),
                         'label' => xarML('View Statistics'));

    if (xarModGetVar('comments', 'useblacklist') == true){
        $menulinks[] = Array('url'   => xarModURL('comments',
                                                  'admin',
                                                  'importblacklist'),
                             'title' => xarML('Import the latest blacklist'),
                             'label' => xarML('Import Blacklist'));
    }

    $menulinks[] = Array('url'   => xarModURL('comments',
                                              'admin',
                                              'modifyconfig'),
                         'title' => xarML('Modify the comments module configuration'),
                         'label' => xarML('Modify Config'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>