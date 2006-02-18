<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @return array containing the menulinks for the main menu items.
 */
function newsletter_userapi_getmenulinks()
{
    $menulinks = array();

    // Check if user is logged in
    if (xarUserIsLoggedIn()) {

        $uid = xarUserGetVar('uid');

        // See if this user has already subscribed
        $subscriptions = xarModAPIFunc('newsletter',
                                       'user',
                                       'getsubscription',
                                       array('id' => 0, // doesn't matter
                                             'uid' => $uid));

        if (count($subscriptions) == 0) {
        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'user',
                                                  'newsubscription'),
                                           'title' => xarML('Subscribe to an newsletter'),
                             'label' => xarML('Subscribe'));
        } else {
            $menulinks[]  = Array('url'   => xarModURL('newsletter',
                                                                'user',
                                                                'modifysubscription'),
                                           'title' => xarML('Modify your subscription to an newsletter'),
                                           'label' => xarML('Modify Subscription'));
    }
    }






    // Show past issues
    $menulinks[] = Array('url'   => xarModURL('newsletter',
                                              'user',
                                              'viewarchives'),
                         'title' => xarML('View archives of past issues'),
                         'label' => xarML('View Archives'));

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}

?>
