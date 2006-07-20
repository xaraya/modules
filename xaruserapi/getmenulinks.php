<?php
/**
 * Standard function to get main menu links
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage wizards
 * @link http://xaraya.com/index.php/release/3007.html
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * Get menu links for the user
 * @return array
 */
function wizards_userapi_getmenulinks()
{

// Security Check
    $wizards = xarModGetVar('wizards','status');
    if (xarSecurityCheck('ViewWizards',0) &&  ($wizards % 2)) {
        $menulinks[] = Array('url'   => xarModURL('wizards',
                                                  'user',
                                                  'listscripts',
                                                  array('info' => xarRequestGetInfo())),
                              'title' => xarML('Display the wizards available to this module'),
                              'label' => xarML('Wizards'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>