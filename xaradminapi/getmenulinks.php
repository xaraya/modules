<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
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
        $menulinks[] = array('url'   => xarModURL(
            'pubsub',
            'admin',
            'viewall'
        ),
                             'title' => xarML('View all Pubsub Subscriptions'),
                             'label' => xarML('View Subscriptions'));
        $menulinks[] = array('url'   => xarModURL(
            'pubsub',
            'admin',
            'viewq'
        ),
                             'title' => xarML('View all events waiting to be processed'),
                             'label' => xarML('View Event Queue'));
        $menulinks[] = array('url'   => xarModURL(
            'pubsub',
            'admin',
            'view_templates'
        ),
                              'title' => xarML('Modify the Pubsub Templates'),
                              'label' => xarML('Modify Templates'));
        $menulinks[] = array('url'   => xarModURL(
            'pubsub',
            'admin',
            'modifyconfig'
        ),
                              'title' => xarML('Modify the Pubsub Configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
