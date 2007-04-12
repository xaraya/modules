<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Sharecontent module development team
 * @return array containing the menulinks for the main menu items.
 */
function sharecontent_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminSharecontent')) {
        $menulinks[] = Array('url'   => xarModURL('sharecontent',
                                                  'admin',
                                                  'webconfig'),
                              'title' => xarML('Enable sharecontent module web sites'),
                              'label' => xarML('Websites config'));
        $menulinks[] = Array('url'   => xarModURL('sharecontent',
                                                  'admin',
                                                  'mailconfig'),
                              'title' => xarML('Modify sharecontent module mail configuration'),
                              'label' => xarML('Mail config'));
        $menulinks[] = Array('url'   => xarModURL('sharecontent',
                                                  'admin',
                                                  'overview'),
                              'title' => xarML('Overview sharecontent module admin'),
                              'label' => xarML('Overview'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
