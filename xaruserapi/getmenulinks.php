<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 */

/**
 * Utility function pass individual menu items to the main menu
 *
 * @author the Surveys module development team
 * @return array containing the menulinks for the main menu items.
 */
function surveys_userapi_getmenulinks()
{
    //if (xarSecurityCheck('ViewSurveys', 0)) {

        $menulinks[] = array('url' => xarModURL('surveys',
                'user',
                'overview'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Overview for surveys'),
            'label' => xarML('Overview'));
    //}
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