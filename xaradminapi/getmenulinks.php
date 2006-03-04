<?php
/**
 * Admin Menu links
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function comments_adminapi_getmenulinks()
{
    $menulinks[] = Array('url'   => xarModURL('comments',
                                              'admin',
                                              'stats'),
                         'title' => xarML('View comments per module statistics'),
                         'label' => xarML('View Statistics'));
    /* Comment blacklist unavailable at 2005-10-12
    if (xarModGetVar('comments', 'useblacklist') == true){
        $menulinks[] = Array('url'   => xarModURL('comments',
                                                  'admin',
                                                  'importblacklist'),
                             'title' => xarML('Import the latest blacklist'),
                             'label' => xarML('Import Blacklist'));
    }
    */

    $menulinks[] = Array('url'   => xarModURL('comments',
                                              'admin',
                                              'modifyconfig'),
                         'title' => xarML('Modify the comments module configuration'),
                         'label' => xarML('Modify Config'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>