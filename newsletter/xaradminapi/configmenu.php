<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * generate the common admin menu configuration
 *
 * @author Richard Cave
 * @returns array
 * @return $menu
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
