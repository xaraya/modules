<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
   utility function pass individual menu items to the main menu

   @return array containing the menulinks for the main menu items.
*/
function security_adminapi_getmenulinks()
{

    $menulinks = array();

    // Security Check
    if( Security::check(SECURITY_READ, 'security', 0, 0, false) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('security', 'admin', 'overview'),
            'title' => xarML('Overview'),
            'label' => xarML('Overview')
        );

        $menulinks[] = Array(
            'url'   => xarModURL('security', 'admin', 'view'),
            'title' => xarML('View Security'),
            'label' => xarML('View Security')
        );

        $menulinks[] = Array(
            'url'   => xarModURL('security', 'admin', 'hook_settings'),
            'title' => xarML('Modify Hook Settings'),
            'label' => xarML('Modify Hook Settings')
        );

    }

    return $menulinks;
}
?>
