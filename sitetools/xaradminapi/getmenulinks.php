<?php
/*
 * File: $Id: getmenulinks.php,v 1.1 2003/09/19 09:16:03 jojodee Exp $
 *
 * Standard utility functin to pass individual menu items to the main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/


/**
 * utility function pass individual menu items to the main menu
 *
 * @author jojodee
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sitetools_adminapi_getmenulinks()
{ 
    $menulinks = array();

     // Security Check
    if (xarSecurityCheck('AdminSiteTools', 0)) {
        // The main menu will look for this array and return it for a tree view of the module
        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'optimize'),
            'title' => xarML('Optimize a database'),
            'label' => xarML('Optimize database'));

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'backup'),
            'title' => xarML('Backup a database'),
            'label' => xarML('Backup database'));

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'cacheview'),
            'title' => xarML('Browse cache files'),
            'label' => xarML('Browse cache files'));

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'deletecache'),
            'title' => xarML('Clear cache files'),
            'label' => xarML('Clear cache files'));

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'links'),
            'title' => xarML('Check URLs and images in articles, roles, ...'),
            'label' => xarML('Check links'));

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }

    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}
?>
