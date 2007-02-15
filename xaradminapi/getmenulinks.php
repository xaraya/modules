<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */

/**
 * utility function pass individual menu items to the main menu
 *
 * @return array
 * @return array containing the menulinks for the main menu items.
 */
function uploads_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditUploads')) {
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
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'assoc'),
                             'title' => xarML('View All Known File Associations'),
                             'label' => xarML('View Associations'));
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
