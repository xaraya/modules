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
 * Display an item
 *
 * This is a standard function to provide detailed information on a single item
 * available from the module.
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param  array $args an array of arguments (if called by other modules)
 * @return array $data The array that contains all data for the template
 */
function twitter_user_display($args)
{
    if (!xarSecurityCheck('ReadTwitter')) return;
    if (!xarVarFetch('screen_name', 'str:1:', $screen_name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab', 'str:1', $tab, 'user_timeline', XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('twitter', 'user', 'menu', array('modtype' => 'user', 'modfunc' => 'display'));

    $defaults = array();
    $settings = array();
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
    /* account identified as belonging to this user */
    if (!empty($data['user_account']) && $screen_name == $data['user_account']['screen_name']) {
      /* account associated (username/password stored in dd prop) */
      if ($data['associated']) return xarResponseRedirect(xarModURL('twitter', 'user', 'account', array('screen_name' => $screen_name)));
      /* if we got here, user is storing screen_name and password as a session var */
      /* we already got the account info, no need to get it again */
      $data['user_element'] = $data['user_account'];
      /* this scenario uses default settings for the module */
      foreach ($defaults as $key => $value) {
        $settings[$key] = !$value ? false : $value;
      } 
    /* account identified as site account */
    } elseif (!empty($data['site_account']) && $screen_name == $data['site_account']['screen_name']) {
      /* user owns the site account */
      if ($data['isowner']) return xarResponseRedirect(xarModURL('twitter', 'user', 'account', array('screen_name' => $screen_name)));
      /* we already got the account info, no need to get it again */
      $data['user_element'] = $data['site_account'];
      /* get settings for viewers of the site account */
      // TODO: where are these coming from?
      foreach ($defaults as $key => $value) {
          if ($value == 2) {
            $setting = xarModGetVar('twitter', 'site_'.$key);
          } else {
            $setting = $value;
          }
          $settings[$key] = !$setting ? false : $setting;
      } 
    /* user doesn't own this account, just get the show method */
    } else {
      // TODO: explore authenticating current user here
      // and see if it's possible to establish relationships
      // eg, if current user is friend or follower of the account being diplayed, 
      // show applicable options such as friends timeline, send direct message, etc.
      $data['user_element'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
        array(
        'area' => 'users',
        'method' => 'show',
        'username' => $screen_name,
        'cached' => true,
        'refresh' => 3600,
        'superrors' => true
      ));
      if (!empty($data['user_element'])) {
        // let's see if it belongs to one of our users by way of a dd prop
        if (!empty($data['t_fieldname'])) {
          $userdd = xarModAPIFunc('dynamicdata', 'user', 'getitems', array('module' => 'roles', 'itemtype' => 0, 'where' => $data['t_fieldname']. ' LIKE "'.$screen_name.'%"'));
        }
        // make sure we only found one user
        // TODO: expand the duplicates and find an exact match
        if (!empty($userdd) && count($userdd) == 1) {
          // this is the user id this screen name belongs to
          $user_id = key($userdd);
          $data['user_element']['uid'] = $user_id;
          // get the display settings for this users account if allowed
          foreach ($defaults as $key => $value) {
            // a value of 2 means users can over-ride this setting
            if ($value == 2) {
              // get the user settings
              $setting = xarModGetUserVar('twitter', $key, $user_id);
            // any other value and we use the module default setting
            } else {
              $setting = $value;
            }
            $settings[$key] = !$setting ? false : $setting;
          }
        // doesn't belong to a user, fall back to admin configured settings
        } else {
          foreach ($defaults as $key => $value) {
            $settings[$key] = !$value ? false : $value;
          }
        }
      }
    }

    // handle the output
    // TODO: this can be simplified, most of the work is done above
    switch ($tab) {
      case 'profile_display':
        default:
        // for profile display, we got everything we need already


      break;
      case 'user_timeline':
        // for user timeline, we need to know if this account is associated with this user
        // remember me option was ticked, so we generously get an up to date version of the timeline
        if (!empty($data['user_account']) && $screen_name == $data['user_account']['screen_name']) {
          $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
            array(
              'area' => 'statuses',
              'method' => 'user_timeline',
              'username' => $screen_name,
              'password' => '',
              'cached' => true,
              'refresh' => 60,
              'superrors' => true
            ));          
        } else {
          $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
            array(
              'area' => 'statuses',
              'method' => 'user_timeline',
              'user_id' => $screen_name,
              'cached' => true,
              'refresh' => 3600,
              'superrors' => true
            ));
        }
      break;
      case 'friends_display':
        $data['user_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
          'area' => 'statuses',
          'method' => 'friends',
          'username' => $screen_name,
          'password' => '',
          'cached' => true,
          'refresh' => 3600,
          'superrors' => true
        ));
      break;
      case 'favourites_display':
        $data['status_elements'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'favorites', 
            'method' => 'favorites',
            'username' => $data['user_element']['screen_name'],
            'password' => '',
            'cached' => true,
            'refresh' => 300,
            'superrors' => true
          ));
      break;
    }
    $displaytabs = array();
    if ((!empty($settings['user_timeline']) && $data['user_element']['protected'] == 'false') || (!empty($data['user_account']) && $data['user_account']['screen_name'] == $screen_name)) {
      $displaytabs[] = array(
        'label' => xarML('Timeline'),
        'url' => xarModURL('twitter', 'user', 'display', array('screen_name' => $screen_name, 'tab' => 'user_timeline')),
        'title' => xarML('View timeline for #(1)', $screen_name),
        'active' => $tab == 'user_timeline' ? true : false
      );
    }
    if ((!empty($settings['friends_display']) && $data['user_element']['protected'] == 'false') || (!empty($data['user_account']) && $data['user_account']['screen_name'] == $screen_name)) {
      $displaytabs[] = array(
        'label' => xarML('Following'),
        'url' => xarModURL('twitter', 'user', 'display', array('screen_name' => $screen_name, 'tab' => 'friends_display')),
        'title' => xarML('View users being followed by #(1)', $screen_name),
        'active' => $tab == 'friends_display' ? true : false
      );
    }
    if ((!empty($settings['favourites_display']) && $data['user_element']['protected'] == 'false') || (!empty($data['user_account']) && $data['user_account']['screen_name'] == $screen_name)) {
      $displaytabs[] = array(
        'label' => xarML('Favourites'),
        'url' => xarModURL('twitter', 'user', 'display', array('screen_name' => $screen_name, 'tab' => 'favourites_display')),
        'title' => xarML('View favourites for #(1)', $screen_name),
        'active' => $tab == 'favourites_display' ? true : false
      );
    }
    $data['displaytabs'] = $displaytabs;
    $data['tab'] = $tab;
    $data['screen_name'] = $screen_name;
    $data['user_settings'] = $settings;

    return $data;
}
?>