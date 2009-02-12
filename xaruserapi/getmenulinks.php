<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_userapi_getmenulinks()
{ 
    /* First we need to do a security check to ensure that we only return menu items
     * that we are suppose to see.
     */
    if (xarSecurityCheck('ViewProgram', 0)) {
        $menulinks[] = array('url' => xarModURL('labaffiliate',
                'user',
                'view'), 
            'title' => xarML('Displays all programs for view'),
            'label' => xarML('View Programs'));
    } 
    if (xarSecurityCheck('ViewProgramAffiliate', 0)) {
        $menulinks[] = array('url' => xarModURL('labaffiliate',
                'affiliate',
                'view'), 
            'title' => xarML('Displays all affiliates for view'),
            'label' => xarML('View Programs'));
    } 
    if (xarSecurityCheck('ViewProgramMembership', 0)) {
        $menulinks[] = array('url' => xarModURL('labaffiliate',
                'membership',
                'view'), 
            'title' => xarML('Displays all members for view'),
            'label' => xarML('View Programs'));
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