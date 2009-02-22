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

    /* Visitors with only view privs */
    if (xarSecurityCheck('ViewTwitter', 0) && xarModGetVar('twitter', 'public_timeline')) {
      $menulinks[] = array(
          'url'   => xarModURL('twitter','user','main'), 
          'title' => xarML('Display twitter timeline'),
          'label' => xarML('Public Timeline')
      );
    }
    /* Visitors with read privs */
    if (xarSecurityCheck('ReadTwitter', 0)) {
      $site_screen_name = xarModGetVar('twitter', 'site_screen_name');
      if (!empty($site_screen_name) && (xarModGetVar('twitter', 'account_display') || xarUserGetVar('uid') == xarModGetVar('site_screen_role'))) {
        $menulinks[] = array(
          'url'   => xarModURL('twitter','user','display', array('screen_name' => $site_screen_name)), 
          'title' => xarML('Display twitter timeline'),
          'label' => $site_screen_name
        );
      }
    }

    if (xarSecurityCheck('CommentTwitter', 0)) {
      $user_screen_name = xarSessionGetVar('twitter_screen_name');
      if (!empty($user_screen_name)) {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','display', array('screen_name' => $user_screen_name)), 
              'title' => xarML('Display twitter timeline'),
              'label' => $user_screen_name
          );
      } else {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','tweet'), 
              'title' => xarML('Send Twitter Update'),
              'label' => xarML('New Tweet')
          );
      }
    }

    if (xarSecurityCheck('ReadTwitter', 0) && xarModGetVar('twitter', 'users_display')) {
      $t_fieldname = xarModGetVar('twitter', 'fieldname');
      if (!empty($t_fieldname)) {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','view'), 
              'title' => xarML('View site twitter users'),
              'label' => xarML('Users')
          );
      }
    }

    if (xarSecurityCheck('EditTwitter', 0)) {
      $t_fieldname = !isset($t_fieldname) ? xarModGetVar('twitter', 'fieldname') : $t_fieldname;
      if (!empty($t_fieldname)) {
        $userdd = xarUserGetVar($t_fieldname);
      }
      if (!empty($userdd) && strpos($userdd, ',') !== false) {
        list($user_screen_name) = explode(',', $userdd);
      }
      if (!empty($user_screen_name)) {
          $menulinks[] = array(
              'url'   => xarModURL('twitter','user','account', array('screen_name' => $user_screen_name)), 
              'title' => xarML('Display twitter timeline'),
              'label' => $user_screen_name
          );
      }
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
} 
?>