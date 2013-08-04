<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    if (xarModVars::get('comments', 'useblacklist') == true){
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