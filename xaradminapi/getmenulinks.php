<?php
/**
 * Utility function to pass admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 * @return array containing the menulinks for the main menu items.
 */
function xproject_adminapi_getmenulinks()
{
    $menulinks = array();

    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');

    if (xarSecurityCheck('ReadXProject', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                  'admin',
                                                  'dashboard'),
                             'title' => xarML('Summary of information on current projects'),
                             'label' => xarML('Project Dashboard'));

        $uid = xarSessionGetVar('uid');
        $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
        if(is_numeric($mymemberid) && $mymemberid > 0) {
            $menulinks[] = Array('url'   => xarModURL('xproject',
                                                      'admin',
                                                      'main',
                                                      array('status' => $draftstatus,
                                                            'mymemberid' => $mymemberid,
                                                            'sortby' => "priority")),
                                 'title' => xarML('Work on your draft projects before submitting'),
                                 'label' => xarML('My Drafts'));
            $menulinks[] = Array('url'   => xarModURL('xproject',
                                                      'admin',
                                                      'main',
                                                      array('status' => $activestatus,
                                                            'mymemberid' => $mymemberid,
                                                            'sortby' => "priority")),
                                 'title' => xarML('View active projects you are part of the team for'),
                                 'label' => xarML('My Active'));
        }

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'main'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Projects'));
    }

    if (xarSecurityCheck('AddXProject', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                  'admin',
                                                  'newcombo'),
                             'title' => xarML('Quick project/contact form'),
                             'label' => xarML('Quick Project Form'));

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a new project'),
                              'label' => xarML('New Project'));
    }

    if (xarSecurityCheck('AdminXProject', 0)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for XProject'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}

?>