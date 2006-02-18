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
 * generate the common admin menu for subscriptions
 *
 * @author Richard Cave
 * @return array $menu
 */
function newsletter_adminapi_subscriptionmenu()
{
    // Initialise the array that will hold the menu configuration
    $menulinks = array();

    // Specify the menu titles to be used in your blocklayout template
    if(xarSecurityCheck('AdminNewsletter', 0)) {

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'searchsubscription'),
                              'page'  => 'searchsubscription',
                              'title' => xarML('Search for a Subscription'),
                              'label' => xarML('Search Subscription'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'newaltsubscription'),
                              'page'  => 'newaltsubscription',
                              'title' => xarML('Add a single subscription'),
                              'label' => xarML('Add Subscription'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'newimportaltsubscription'),
                              'page'  => 'newimportaltsubscription',
                              'title' => xarML('Import Subscriptions'),
                              'label' => xarML('Import Subscriptions'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewsubscription'),
                              'page'  => 'viewsubscription',
                              'title' => xarML('View the Subscriptions'),
                              'label' => xarML('View Subscriptions'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    // Return the array containing the menu configuration
    return $menulinks;
}

?>
