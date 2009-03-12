<?php
/**
 * Items for Admin Menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * utility function pass individual menu items to the admin menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function uploads_adminapi_getmenulinks()
{
    static $menulinks = array();
    if (isset($menulinks[0])) {
        return $menulinks;
    }
    if (xarSecurityCheck('EditUploads')) {
     $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'view'),
                             'active' => array('view'
                                              ,'main'
                              ),
                             'title' => xarML('View All Files'),
                             'label' => xarML('View Files'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'get_files'),
                             'active' => array('get_files'
                                              ,'addfile-status'
                                              ,'purge_rejected'
                              ),
                             'title' => xarML('Add a File'),
                             'label' => xarML('Add File'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'assoc'),
                             'active' => array('assoc'),
                             'title' => xarML('View All Known File Associations'),
                             'label' => xarML('View Associations'));
    }
    if (xarSecurityCheck('AdminUploads')) {
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'modifyconfig'),
                             'active' => array('modifyconfig'),
                             'title' => xarML('Edit the Uploads Configuration'),
                             'label' => xarML('Modify Config'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                  'admin',
                                                  'overview'
                            )
                            ,'active' => array('overview')
                            ,'title' => xarML('Introduction on handling this module')
                            ,'label' => xarML('Overview')
        );
    }
    return $menulinks;
}
?>
