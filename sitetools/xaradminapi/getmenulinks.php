<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by jojodee
 * @link http://xaraya.athomeandabout.come
 *
 * @subpackage SiteTools module
 * @author Jo Dalle Nogare <http://xaraya.athomeandabout.com  contact:jojodee@xaraya.com>
*/


/**
 * utility function pass individual menu items to the main menu
 *
 * @author jojodee, http://xaraya.athomeandabout.com
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sitetools_adminapi_getmenulinks()
{ 
     // Security Check
    if (xarSecurityCheck('AdminSiteTools', 0)) {
        // The main menu will look for this array and return it for a tree view of the module
        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'optimize'),
            'title' => xarML('Optimize a database'),
            'label' => xarML('Optimize'));
    }
    // Security Check
    if (xarSecurityCheck('AdminSiteTools', 0)) {

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'backup'),
            'title' => xarML('Backup a database'),
            'label' => xarML('Backup'));
    }
    // Security Check
    if (xarSecurityCheck('AdminSiteTools', 0)) {

        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'deletecache'),
            'title' => xarML('Clear cache files'),
            'label' => xarML('Clear cache files'));
    }
    // Security Check
    if (xarSecurityCheck('AdminSiteTools', 0)) {
        $menulinks[] = Array('url' => xarModURL('sitetools',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    }
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
}
?>
