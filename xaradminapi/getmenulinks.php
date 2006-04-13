<?php
/**
 * Pubsub Admin API
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function pubsub_adminapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('AdminPubSub', 0)) {
        $menulinks[] = Array('url'   => xarModURL('pubsub',
                                                  'admin',
                                                  'viewall'),
                             'title' => xarML('View all Pubsub Subscriptions'),
                             'label' => xarML('View Subscriptions'));
        $menulinks[] = Array('url'   => xarModURL('pubsub',
                                                  'admin',
                                                  'viewq'),
                             'title' => xarML('View all events waiting to be processed'),
                             'label' => xarML('View Event Queue'));
        $menulinks[] = Array('url'   => xarModURL('pubsub',
                                                  'admin',
                                                  'modifytemplates'),
                              'title' => xarML('Modify the Pubsub Templates'),
                              'label' => xarML('Modify Templates'));
        $menulinks[] = Array('url'   => xarModURL('pubsub',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the Pubsub Configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;

} // END getmenulinks

?>
