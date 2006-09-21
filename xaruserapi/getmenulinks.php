<?php
/**
 * Utility function pass individual menu items to the main menu
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
 * Utility function pass individual menu items to the main menu
 *
 * @author the ITSP module development team
 * @return array containing the menulinks for the main menu items.
 */
function itsp_userapi_getmenulinks()
{
    /* First we need to do a security check to ensure that we only return menu items
     * that we are suppose to see.
     */
    if (xarSecurityCheck('ViewITSPPlan', 0)) {
        $menulinks[] = array('url' => xarModURL('itsp',
                'user',
                'view'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('View our education plans'),
            'label' => xarML('Plans'));
    }
    if (xarSecurityCheck('ReadITSP', 0)) {
        $menulinks[] = array('url' => xarModURL('itsp',
                'user',
                'itsp'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('View your education plan'),
            'label' => xarML('My ITSP'));
    }
    if (xarSecurityCheck('EditITSP', 0)) {
        $menulinks[] = array('url' => xarModURL('itsp',
                'user',
                'review'),
            'title' => xarML('Review the ITSPs'),
            'label' => xarML('Review ITSPs'));
    }
    /* If we return nothing, then we need to tell PHP this, in order to avoid an ugly
     * E_ALL error.
     */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}
?>