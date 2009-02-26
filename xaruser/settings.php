<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * View a list of items
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array $data array with all information for the template
 */
function twitter_user_settings($args)
{
    if (!xarSecurityCheck('EditTwitter')) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('screen_name', 'str:1', $screen_name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str:1', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    $data = xarModAPIFunc('twitter', 'user', 'menu', array('modtype' => 'user', 'modfunc' => 'settings'));

    if (!empty($data['site_account']) && $screen_name == $data['site_account']['screen_name'] && $data['isowner']) {
      $varprefix = 'site_';
    } else {
      $varprefix = '';
    }
    $defaults = array();
    // get module defaults
    $defaults['user_timeline'] = xarModGetVar('twitter', 'user_timeline');
    $defaults['friends_display'] = xarModGetVar('twitter', 'friends_display');
    $defaults['profile_image'] = xarModGetVar('twitter', 'profile_image');
    $defaults['profile_description'] = xarModGetVar('twitter', 'profile_description');
    $defaults['profile_location'] = xarModGetVar('twitter', 'profile_location');
    $defaults['followers_count'] = xarModGetVar('twitter', 'followers_count');
    $defaults['friends_count'] = xarModGetVar('twitter', 'friends_count');
    $defaults['last_status'] = xarModGetVar('twitter', 'last_status');  
    $defaults['profile_url'] = xarModGetVar('twitter', 'profile_url');
    $defaults['statuses_count'] = xarModGetVar('twitter', 'statuses_count');  
    $defaults['favourites_display'] = xarModGetVar('twitter', 'favourites_display');

    if ($phase == 'update') {
      if (!xarSecConfirmAuthKey()) return;
      foreach ($defaults as $k => $v) {
        if ($v == 2) {
          if (!xarVarFetch($k, 'checkbox', $curval, false, XARVAR_NOT_REQUIRED)) return;
        } else {
          $curval = empty($v) ? false : $v;
        }
        if (empty($varprefix)) {
          xarModSetUserVar('twitter', $k, $curval);
        } else {
          xarModSetVar('twitter', $varprefix.$k, $curval);
        }
        unset($curval);
      }
      xarSessionSetVar('statusmsg', xarML('Twitter Settings Updated Successfully'));
      if (empty($returnurl)) $returnurl = xarModURL('twitter', 'user', 'settings');
      xarResponseRedirect($returnurl);
      return true;
    }
   
    return $data;
}
?>