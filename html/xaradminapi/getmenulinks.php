<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Utility function pass individual menu items to the main menu
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function html_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AddHTML')) {

        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new tag.'),
                              'label' => xarML('Add Tag'));

        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'newtype'),
                              'title' => xarML('Add a new tag type for use on your site.'),
                              'label' => xarML('Add Tag Type'));
    }

    if (xarSecurityCheck('AdminHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'set'),
                              'title' => xarML('Set the allowed tags for use on your site'),
                              'label' => xarML('Set Tags'));
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration of the HTML Module'),
                              'label' => xarML('Modify Config'));
    }

    if (xarSecurityCheck('ReadHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                  'admin',
                                                  'viewtypes'),
                              'title' => xarML('View and edit tag types.'),
                              'label' => xarML('View Tag Types'));
    }

/*
    if (xarSecurityCheck('AdminHTML')) {
        $menulinks[] = Array('url'   => xarModURL('html',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the HTML Module'),
                              'label' => xarML('Modify Config'));
    }
*/
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
