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
 * Utility function pass individual menu items to the main menu
 * 
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array containing the menulinks for the main menu items.
 */
function twitter_userapi_getmenulinks()
{ 
    if (xarSecurityCheck('ViewTwitter', 0)) {
        if (xarModGetVar('twitter', 'showpublic')) {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','main'), 
              'title' => xarML('Display twitter timeline'),
              'label' => xarML('Public Timeline')
          );
        }
        if (xarModGetVar('twitter', 'showuser')) {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','main', array('timeline' => 'user')), 
              'title' => xarML('Display twitter timeline'),
              'label' => xarML('#(1) Timeline', xarModGetVar('twitter', 'username'))
          );
        }
        if (xarModGetVar('twitter', 'showfriends')) {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','main', array('timeline' => 'friends')), 
              'title' => xarML('Display twitter timeline'),
              'label' => xarML('Friends Timeline')
          );
        }
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
} 
?>