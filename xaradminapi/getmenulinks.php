<?php
/**
 * Menu items
 * 
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008,2009 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Utility function pass individual menu items to the main menu
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function sitecontact_adminapi_getmenulinks()
{
    $menulinks = array();
     /*Security Check */
    if (xarSecurityCheck('EditSiteContact', 0)) {
       $menulinks[] = Array('url' => xarModURL('sitecontact','admin','managesctypes'),
            'title' => xarML('Manage the contact forms'),
            'label' => xarML('Manage Contact Forms'),
            'active'=> array('managesctypes')
            );
    
        $menulinks[] = Array('url' => xarModURL('sitecontact','admin','view'),
            'title' => xarML('Manage contact form responses'),
            'label' => xarML('Review Responses'),
            'active' => array('view','display','delete'),
            'activelabels' => array('',xarML('Display'),xarML('Delete'))
            );
    }
    if (xarSecurityCheck('AdminSiteContact', 0)) {            
        $menulinks[] = Array('url' => xarModURL('sitecontact','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'),
            'active' => array('modifyconfig')
            );
    
    }            
      /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}

?>