<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */

/**
 * Pass individual menu items to the admin  menu
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array containing the menulinks for the main menu items.
 */
function twitter_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminTwitter', 0)) {
        $menulinks[] = array('url' => xarModURL('twitter','admin','modifyconfig'),
            'title' => xarML('Modify the configuration for the module'),
            'label' => xarML('Modify Config'));
        $menulinks[] = array('url' => xarModURL('twitter','admin','overview'),
            'title' => xarML('View module overview'),
            'label' => xarML('Overview'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
