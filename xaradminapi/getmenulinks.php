<?php
/**
 * Xarigami Formantibot
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function formantibot_adminapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
  
    if (xarSecurityCheck('AdminAkismet',0)) {

        $menulinks[] = Array('url'   => xarModURL('formantibot', 'admin',  'modifyconfig'),
                              'title' => xarML('Modify the akismet module configuration'),
                              'label' => xarML('Modify Config'));
    
       $menulinks[] = Array('url'   => xarModURL('formantibot', 'admin', 'overview'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));
    
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>