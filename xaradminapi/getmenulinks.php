<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
   utility function pass individual menu items to the main menu

   @return array containing the menulinks for the main menu items.
*/
function helpdesk_adminapi_getmenulinks()
{
    $menulinks = array();
    if( Security::check(SECURITY_ADMIN, 'helpdesk', 0, 0, false) )
    {
        $menulinks[] = array('url'   => xarModURL('helpdesk', 'admin', 'view'),
            'title' => xarML('View Items'),
            'label' => xarML('View Items')
        );
        /* TODO: Currently missing function */
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin', 'setup_security'),
            'title' => xarML('Setup Security'),
            'label' => xarML('Setup Security')
        );
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin', 'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config')
        );
    }
    // Check for emptiness
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
