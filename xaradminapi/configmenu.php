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
 * generate the common admin menu configuration
 *
 * @author Richard Cave
 * @return array $menu
 */
function newsletter_adminapi_configmenu()
{
    // Initialise the array that will hold the menu configuration
    $menulinks = array();

    // Specify the menu titles to be used in your blocklayout template
    if(xarSecurityCheck('AdminNewsletter', 0)) {

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'configdesc'),
                              'page'  => 'configdesc',
                              'title' => xarML('Description of the module'),
                              'label' => xarML('Description'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'modifyconfig'),
                              'page'  => 'modifyconfig',
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'modifyprivileges'),
                              'page'  => 'modifyprivileges',
                              'title' => xarML('Modify the groups and privileges for the module'),
                              'label' => xarML('Modify Privileges'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewusers'),
                              'page'  => 'viewusers',
                              'title' => xarML('Modify the users the module'),
                              'label' => xarML('Modify Users'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'viewpublication'),
                              'page'  => 'viewpublication',
                              'title' => xarML('View all Newsletter Publications'),
                              'label' => xarML('View Publications'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'newpublication'),
                              'page'  => 'newpublication',
                              'title' => xarML('Create a Newsletter Publication'),
                              'label' => xarML('Create Newsletter'));

        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'admin',
                                                  'modifynewsletter'),
                              'page'  => 'modifynewsletter',
                              'title' => xarML('Modify the configuration for a newsletter'),
                              'label' => xarML('Modify Newsletter'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    // Return the array containing the menu configuration
    return $menulinks;
}

?>
