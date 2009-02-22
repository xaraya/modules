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
 * The user account function
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param tab - optional (default user-timeline) 
 * @param screen_name - required screen name to display account info for
 * @return array $data An array with the data for the template
 */
function twitter_user_account()
{

    if (!xarSecurityCheck('EditTwitter')) return;
    if (!xarVarFetch('tab', 'str:1:', $tab, 'friends_timeline', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('screen_name', 'str:1:', $screen_name, '', XARVAR_NOT_REQUIRED)) return;

    if (empty($screen_name)) {
      $msg = xarML('Invalid screen name.');
      xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new DefaultUserException($msg));
      return;
    }

    // this gets everything we need
    $data = xarModAPIFunc('twitter', 'user', 'menu', 
      array('modtype' => 'user', 'modfunc' => 'account', 'screen_name' => $screen_name));

    // if we're viewing the site account, make sure we're the owner */
    if (!empty($data['site_account']) && $screen_name == $data['site_account']['screen_name'] && $data['isowner']) {
      $data['user_element'] = $data['site_account'];
      $screen_pass = xarModGetVar('twitter', 'site_screen_pass');
    // otherwise, we must be viewing a user account, make sure it belongs to this user
    } else {
      if (!empty($data['user_account']) && $screen_name == $data['user_account']['screen_name']) {
        $data['user_element'] = $data['user_account'];
        $screen_pass = $data['user_screen_pass'];
      // no accounts found, return to user display for this screen name
      } else {
        return xarResponseRedirect(xarModURL('twitter', 'user', 'display', array('screen_name' => $screen_name)));
      }
    }
    $defaults = array();
    $user_settings = array();
    $showedit = false;
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
    foreach ($defaults as $key => $value) {
      if (empty($value)) continue;
      // a value of 2 means users can over-ride this setting
      if ($value == 2) {
        $showedit = true;
        // get the user settings
        if (!empty($data['site_account']) && $screen_name == $data['site_account']['screen_name'] && $data['isowner']) {
          $setting = xarModGetVar('twitter', 'site_'.$key);
        } else {
          $setting = xarModGetUserVar('twitter', $key);
        }
        $user_settings[$key] = $setting !== null ? $setting : $value;
      } else {
        $user_settings[$key] = $value !== null ? $value : false;
      }
    }
    $data['user_settings'] = $user_settings;
    
    /* start output */
    switch ($tab) {
      case 'user_timeline':
        $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'statuses',
            'method' => $tab,
            'username' => $data['user_element']['screen_name'],
            'password' => $screen_pass,
            'cached' => true,
            'refresh' => 300,
            'superrors' => true
          ));
      break;
      case 'friends_timeline':
        $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'statuses',
            'method' => $tab,
            'username' => $data['user_element']['screen_name'],
            'password' => $screen_pass,
            'cached' => true,
            'refresh' => 300,
            'superrors' => true
          ));
      break;
      case 'profile_display':

      break;
      case 'profile_edit':
        $edit_options = array();
        foreach ($defaults as $key => $value) {
          // a value of 2 means users can over-ride this setting
          if ($value == 2) {
            // get the user settings
            if (!empty($data['site_account']) && $screen_name == $data['site_account']['screen_name'] && $data['isowner']) {
              $setting = xarModGetVar('twitter', 'site_'.$key);
            } else {
              $setting = xarModGetUserVar('twitter', $key);
            }
            $edit_options[$key] = $setting !== null ? $setting : $value;
          } 
        }
        $data['edit_options'] = $edit_options;
      break;
      case 'friends_display':
        $data['user_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
          'area' => 'statuses',
          'method' => 'friends',
          'username' => $data['user_element']['screen_name'],
          'password' => $screen_pass,
          'cached' => true,
          'refresh' => 300,
          'superrors' => true
        ));
      break;
      case 'followers_display':
        $data['user_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
          'area' => 'statuses',
          'method' => 'followers',
          'username' => $data['user_element']['screen_name'],
          'password' => $screen_pass,
          'cached' => true,
          'refresh' => 300,
          'superrors' => true
        ));
      break;
      case 'direct_messages':
        $data['message_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
          'area' => 'direct_messages',
          'method' => 'direct_messages',
          'username' => $data['user_element']['screen_name'],
          'password' => $screen_pass,
          'cached' => true,
          'refresh' => 300,
          'superrors' => true
        ));

      break;
      case 'favourites':
        $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'favorites', 
            'method' => 'favorites',
            'username' => $data['user_element']['screen_name'],
            'password' => $screen_pass,
            'cached' => true,
            'refresh' => 300,
            'superrors' => true
          ));
      break;
      case 'replies_display':
        $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'statuses', 
            'method' => 'replies',
            'username' => $data['user_element']['screen_name'],
            'password' => $screen_pass,
            'cached' => true,
            'refresh' => 300,
            'superrors' => true
          ));
      break;
    }
    
    $data['tab'] = $tab;
    $data['screen_name'] = $screen_name;
    
    $accounttabs = array();

    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'friends_timeline', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'friends_timeline', 
      'label' => xarML('Home'),
      'title' => xarML('Your twitter timeline including friends statuses'),
      'active' => $tab == 'friends_timeline' ? true : false
    );
    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'user_timeline', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'user_timeline', 
      'label' => xarML('Updates'),
      'title' => xarML('Your twitter timeline'),
      'active' => $tab == 'user_timeline' ? true : false
    );
    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'replies_display', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'replies_display', 
      'label' => xarML('@Replies'),
      'title' => xarML('Your twitter @Replies'),
      'active' => $tab == 'replies_display' ? true : false
    );
    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'friends_display', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'friends_display', 
      'label' => xarML('Following'),
      'title' => xarML('Your twitter friends'),
      'active' => $tab == 'friends_display' ? true : false
    );
    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'followers_display', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'followers_display', 
      'label' => xarML('Followers'),
      'title' => xarML('Your twitter followers'),
      'active' => $tab == 'followers_display' ? true : false
    );
    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'direct_messages', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'direct_messages', 
      'label' => xarML('Messages'),
      'title' => xarML('Your twitter direct messages'),
      'active' => $tab == 'direct_messages' ? true : false
    );
    $accounttabs[] = array(
      'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'favourites', 'screen_name' => $data['user_element']['screen_name'])),
      'id' => 'favourites', 
      'label' => xarML('Favourites'),
      'title' => xarML('Your twitter favourites'),
      'active' => $tab == 'favourites' ? true : false
    );  
    if ($showedit) {
      $accounttabs[] = array(
        'url' => xarModURL('twitter', 'user', 'account', array('tab' => 'profile_edit', 'screen_name' => $data['user_element']['screen_name'])),
        'id' => 'profile_edit', 
        'label' => xarML('Edit'),
        'title' => xarML('Edit your twitter account settings'),
        'active' => $tab == 'profile_edit' ? true : false
      );    
    }
    $data['accounttabs'] = $accounttabs;
    /* Return the template variables defined in this function */
    return $data;

}
?>