<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Utility function to pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @return array containing the menulinks for the main menu items.
 */
function newsletter_adminapi_getmenulinks()
{
    $menulinks = array();
     if(xarSecurityCheck('AdminNewsletter', 0)) {

        $menulinks[] = Array('url'   => xarModURL('newsletter','admin','overview'),
                             'title' => xarML('Newsletter overview'),
                             'label' => xarML('Overview'));
        }

    if(xarSecurityCheck('EditNewsletter', 0)) {

        $menulinks[] = Array('url'   => xarModURL('newsletter','admin','selectpublication'),
                             'title' => xarML('Publish a new issue update.'),
                             'label' => xarML('Publish Issue'));

        $menulinks[] = Array('url'   => xarModURL('newsletter','admin','viewissue'),
                             'title' => xarML('Edit a Newsletter.'),
                             'label' => xarML('Edit Issues'));

        $menulinks[] = Array('url'   => xarModURL('newsletter','admin','searchsubscription'),
                              'title' => xarML('Add, Edit and Search for Subscriptions'),
                              'label' => xarML('Subscriptions'));
    }

    if(xarSecurityCheck('AdminNewsletter', 0)) {

        $menulinks[] = Array('url'   => xarModURL('newsletter','admin','configdesc'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}

?>
