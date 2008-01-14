<?php
/**
 * Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2006-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */

/**
 * Utility function pass individual menu items to the main menu
 *
 * @author the JpGraph module development team
 * @return array containing the menulinks for the main menu items.
 */
function jpgraph_userapi_getmenulinks()
{
    /* First we need to do a security check to ensure that we only return menu items
     * that we are suppose to see.
     */
    if (xarSecurityCheck('ViewJpGraph', 0)) {
        $menulinks[] = array('url' => xarModURL('jpgraph',
                'user',
                'main'),
            /* In order to display the tool tips and label in any language,
             * we must encapsulate the calls in the xarML in the API.
             */
            'title' => xarML('Main user interface'),
            'label' => xarML('Main'));
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