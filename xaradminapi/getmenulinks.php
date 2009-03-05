<?php
/**
 * Pass menu items to main menu and in-page menu
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @link http://xaraya.com/index.php/release/802.html
 *
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */

function newsgroups_adminapi_getmenulinks()
{
    static $menulinks = array();
    if (isset($menulinks[0])) {
        return $menulinks;
    }

    if (xarSecurityCheck('AdminNewsGroups', 0)) {
        $menulinks[] = Array('url'   => xarModURL('newsgroups',
                                                  'admin',
                                                  'selectgroups'),
                              'active'=> array('selectgroups'),
                              'title' => xarML('Select the newsgroups you want to display'),
                              'label' => xarML('Select Newsgroups'));
        $menulinks[] = Array('url'   => xarModURL('newsgroups',
                                                  'admin',
                                                  'modifyconfig'),
                              'active'=> array('modifyconfig'),
                              'title' => xarML('Modify the configuration for the newsgroups'),
                              'label' => xarML('Modify Config'));
        $menulinks[] = Array('url'   => xarModURL('newsgroups',
                                                  'admin',
                                                  'overview'),
                              'active'=> array('overview'),
                              'title' => xarML('Introduction on handling this module'),
                              'label' => xarML('Overview'));
    }

    return $menulinks;
}
?>
