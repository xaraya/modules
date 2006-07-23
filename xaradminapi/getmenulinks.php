<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Pubsub module development team
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

}
?>
