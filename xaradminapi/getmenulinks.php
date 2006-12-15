<?php
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Pass the admin items to the admin menu
 *
 * @author the ITSP module development team
 * @return array containing the menulinks for the main menu items.
 */
function itsp_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AddITSPPlan', 0)) {

        $menulinks[] = Array('url' => xarModURL('itsp','admin','new'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Adds a new plan.'),
            'label' => xarML('Add Plan'));

        $menulinks[] = Array('url' => xarModURL('itsp','admin','new_pitem'),
            'title' => xarML('Adds a new planitem.'),
            'label' => xarML('Add Plan Item'));
    }
    /* Security Check */
    if (xarSecurityCheck('EditITSPPlan', 0)) {
        $menulinks[] = Array('url' => xarModURL('itsp','admin','view'),
            'title' => xarML('View all itsp items that have been added.'),
            'label' => xarML('Plans'));

        $menulinks[] = Array('url' => xarModURL('itsp','admin','view_pitems'),
            'title' => xarML('View all plan items that have been added.'),
            'label' => xarML('Plan items'));
    }
    /* Security Check */
    if (xarSecurityCheck('AdminITSP', 0)) {
        $menulinks[] = Array('url' => xarModURL('itsp','admin','modifyconfig'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    /* If we return nothing, then we need to tell PHP this, in order to avoid an ugly
     * E_ALL error.
     */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>