<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Headlines module development team
 * @return array containing the menulinks for the main menu items.
 */
function headlines_adminapi_getmenulinks()
{
    // Security Check
    if(xarSecurityCheck('AddHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new Headline into the system'),
                              'label' => xarML('Add'));
    }
    if(xarSecurityCheck('EditHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit Headlines'),
                              'label' => xarML('View'));
    }
    if(xarSecurityCheck('AdminHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the Headlines Configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>