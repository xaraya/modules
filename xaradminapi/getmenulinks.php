<?php
/**
 * Utility function used in Admin Menu generation
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function images_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminImages')) {
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
