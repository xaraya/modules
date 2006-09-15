<?php
/**
 * Utility function to pass admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the XProject module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xproject_userapi_getmenulinks()
{
    $menulinks = array();
    
    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');

    if (xarSecurityCheck('ReadXProject', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'view'),
                              'title' => xarML('List of current projects'),
                              'label' => xarML('View Projects'));

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'main'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Projects'));
        if (xarUserIsLoggedIn()) {
            $menulinks[] = Array('url'   => xarModURL('xproject',
                                                      'admin',
                                                      'newcombo'),
                                 'title' => xarML('Quick project/contact form'),
                                 'label' => xarML('New Project'));
        
            $uid = xarSessionGetVar('uid');
            $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
            if(is_numeric($mymemberid) && $mymemberid > 0) {
                $menulinks[] = Array('url'   => xarModURL('xproject',
                                                          'admin',
                                                          'main',
                                                          array('status' => $draftstatus,
                                                                'mymemberid' => $mymemberid)),
                                     'title' => xarML('Work on your draft projects before submitting'),
                                     'label' => xarML('My Drafts'));
                $menulinks[] = Array('url'   => xarModURL('xproject',
                                                          'admin',
                                                          'main',
                                                          array('status' => $activestatus,
                                                                'mymemberid' => $mymemberid)),
                                     'title' => xarML('View active projects you are part of the team for'),
                                     'label' => xarML('My Active'));
            }
            $menulinks[] = Array('url'   => xarModURL('xproject',
                                                      'user',
                                                      'settings'),
                                 'title' => xarML('Change your preferences for this module'),
                                 'label' => xarML('Settings'));
        }
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>