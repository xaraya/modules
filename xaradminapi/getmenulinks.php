<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @author the Surveys module development team
 * @return array containing the menulinks for the main menu items.
 */
function surveys_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminSurvey', 0)) {

        $menulinks[] = Array('url' => xarModURL('surveys','admin','viewgroups'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('View groups.'),
            'label' => xarML('View groups'));
    }
    if (xarSecurityCheck('AddSurvey', 0)) {
    $menulinks[] = Array('url' => xarModURL('surveys','admin','newquestion'),

            'title' => xarML('Add question'),
            'label' => xarML('Add a Question'));
    }
    if (xarSecurityCheck('AdminSurvey', 0)) {
    $menulinks[] = Array('url' => xarModURL('surveys','admin','modifygroup'),

            'title' => xarML('Add group'),
            'label' => xarML('Add Group'));
    }
    if (xarSecurityCheck('AdminSurvey', 0)) {
    $menulinks[] = Array('url' => xarModURL('surveys','admin','exportsql'),

            'title' => xarML('exportsql'),
            'label' => xarML('exportsql No Clue'));
    }

    if (xarSecurityCheck('AdminSurvey', 0)) {

        $menulinks[] = Array('url' => xarModURL('surveys','admin','modifyconfig'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Modify Configuration.'),
            'label' => xarML('Modify Config'));
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