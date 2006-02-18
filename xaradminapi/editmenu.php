<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * generate the common admin menu configuration
 *
 * @author Richard Cave
 * @return array $menu
 */
function newsletter_adminapi_editmenu()
{
    // Initialise the array that will hold the menu configuration
    $menulinks = array();

    // Specify the menu titles to be used in your blocklayout template


    if(xarSecurityCheck('AdminNewsletter', 0)) {
        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewpublication'),
                             'page'  => 'viewpublication',
                             'title' => xarML('Edit publications.'),
                             'label' => xarML('Edit Publications'));
    }
    if(xarSecurityCheck('EditNewsletter', 0)) {
        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewissue'),
                             'page'  => 'viewissue',
                             'title' => xarML('Edit issues.'),
                             'label' => xarML('Edit Issues'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewstory'),
                             'page'  => 'viewstory',
                             'title' => xarML('Edit stories.'),
                             'label' => xarML('Edit stories'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewdisclaimer'),
                             'page'  => 'viewdisclaimer',
                             'title' => xarML('Edit disclaimers.'),
                             'label' => xarML('Edit disclaimers'));

        // Check to see if this user is a publication owner
        $userId = xarSessionGetVar('uid');
        $owner = xarModAPIFunc('newsletter',
                               'user',
                               'getowner',
                               array('id' => $userId));

        if ($owner) {
            $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                      'admin',
                                                      'modifysignature'),
                                 'page'  => 'modifysignature',
                                 'title' => xarML('Edit your signature.'),
                                 'label' => xarML('Edit Signature'));
        }
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    // Return the array containing the menu configuration
    return $menulinks;
}

?>
