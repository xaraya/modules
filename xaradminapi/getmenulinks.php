<?php
/**
 * Pass individual menu items to the admin menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAffiliate Module
 * @link http://xaraya.com/index.php/release/892.html
 * @author labaffiliate Module Development Team
 */

/**
 * Pass individual menu items to the admin  menu
 *
 * @author the labaffiliate module development team
 * @return array containing the menulinks for the main menu items.
 */
function labaffiliate_adminapi_getmenulinks()
{
  
    if (xarSecurityCheck('AddProgram', 0)) {

        $menulinks[] = array('url' => xarModURL('labaffiliate','admin','new'),
            'title' => xarML('Adds a new program.'),
            'label' => xarML('New Program'));
    }
    /* Security Check */
    if (xarSecurityCheck('EditProgram', 0)) {
        $menulinks[] = array('url' => xarModURL('labaffiliate','admin','view'),
            'title' => xarML('View Programs.'),
            'label' => xarML('Manage Programs'));
    }
    /* Security Check */
    if (xarSecurityCheck('AdminProgram', 0)) {
        $menulinks[] = array('url' => xarModURL('labaffiliate','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
    }
    if (xarSecurityCheck('ViewProgram', 0)) {
        $menulinks[] = array('url' => xarModURL('labaffiliate','admin','overview'),
            'title' => xarML('Read a general description of labAffiliate'),
            'label' => xarML('Overview'));
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
