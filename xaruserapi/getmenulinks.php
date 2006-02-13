<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Utility function pass individual menu items to the main menu
 *
 * @author jojodee
 * @return array containing the menulinks for the main menu items.
 */
function legis_userapi_getmenulinks()
{ 

   if (xarSecurityCheck('ViewLegis', 0)) {
        $menulinks[] = array('url' => xarModURL('legis','user','main'),
                             'title' => xarML('Legislation Overview'),
                             'label' => xarML('Legislation Overview'));
        $menulinks[] = array('url' => xarModURL('legis','user','view'),
                             'title' => xarML('Legislation Listings'),
                             'label' => xarML('Legislation Listings'));
        $menulinks[] = array('url' => xarModURL('legis','user','view',array('docstatus'=>1)),
                             'title' => xarML('Pending Legislation'),
                             'label' => xarML('Legislation Pending'));
    }
    if (xarSecurityCheck('SubmitLegis', 0)) {
              $menulinks[] = array('url' => xarModURL('legis','user','addlegis'),
                             'title' => xarML('Legislation Submit'),
                             'label' => xarML('Legislation Submit'));
    }
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
