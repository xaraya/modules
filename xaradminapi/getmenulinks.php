<?php
/**
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Censor Module
 * @author John Cox
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Censor module development team
 * @return array containing the menulinks for the main menu items.
 */
function censor_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditCensor')) {
        $menulinks[] = Array('url' => xarModURL('censor','admin','view'),
            'title' => xarML('View and Edit Censored Words'),
            'label' => xarML('View'));
    }

    if (xarSecurityCheck('AddCensor')) {
        $menulinks[] = Array('url' => xarModURL('censor','admin','new'),
            'title' => xarML('Add a new censored word into the system'),
            'label' => xarML('Add'));
    }

    if (xarSecurityCheck('AdminCensor')) {
        $menulinks[] = Array('url' => xarModURL('censor','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the Censor Module'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}
?>
