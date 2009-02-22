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
 * Utility function to prep menu for display
 * 
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array containing the menulinks for the main menu items.
 */
function twitter_userapi_menu($args)
{ 
    extract($args);
    /* set some defaults */
    $menu = $data = array();
    $menu_links = array();
    $site_account = '';
    $site_status = '';
    $user_account = '';
    $user_status = '';

    $site_screen_name = xarModGetVar('twitter', 'site_screen_name');
    $site_screen_pass = xarModGetVar('twitter', 'site_screen_pass');
    $site_screen_role = xarModGetVar('twitter', 'site_screen_role');

    $site_owner = $site_screen_role == xarUserGetVar('uid') ? true : false;

    /* fetch the site account */
    if (!empty($site_screen_name)) {
      /* if we got a password and current user is owner, we validate account */
      if (!empty($site_screen_pass) && $site_owner) {
        $site_account = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'account',
            'method' => 'verify_credentials',
            'username' => $site_screen_name,
            'password' => $site_screen_pass,
            'cached' => true,
            'refresh' => 60,
            'superrors' => true
          ));
      /* otherwise we use the standard show method */
      } else {
        $site_account = xarModAPIFunc('twitter', 'user', 'rest_methods',
          array(
            'area' => 'users',
            'method' => 'show',
            'username' => $site_screen_name,
            'cached' => true,
            'refresh' => 3600,
            'superrors' => true
          ));
      }
    }
    
    /* if current user is owner of the site account, we get them the current rate limit status */
    if (!empty($site_account) && $site_owner) {
      $site_status = xarModAPIFunc('twitter', 'user', 'rest_methods',
        array(
          'area' => 'account',
          'method' => 'rate_limit_status',
          'username' => $site_screen_name, 
          'password' => $site_screen_pass,
          'cached' => false,
          'superrors' => true
        ));   
    }
    
    $data['site_account'] = $site_account;
    $data['site_status'] = $site_status;
    $data['isowner'] = $site_owner;
    $data['owner'] = $site_screen_role;

    /* Handle current user */
    $associated = false;
    $rememberme = false;
    /* check for logged in user */
    if (xarUserIsLoggedIn()) {
      /* Is this user allowed to manage their twitter account here? */
      if (xarSecurityCheck('EditTwitter', 0)) {
        /* look for the twitterscreenname property for this user */
        $t_fieldname = xarModGetVar('twitter', 'fieldname');
        if (!empty($t_fieldname)) {
          /* if we found a fieldname, we get the dd object property */
          $object = xarModAPIFunc('dynamicdata', 'user', 'getitem',
            array(
              'module' => 'roles', 
              'itemtype' => 0, 
              'itemid' => xarUserGetVar('uid'), 
              'fieldlist' => $t_fieldname
            ));
          /* we got a valid property, so we get its value */
          if (isset($object[$t_fieldname])) {
            $user_screen_name = $object[$t_fieldname];
          /* no property found, delete the fieldname modvar */
          } else {
            // forces looking for object once more
            xarModDelVar('twitter', 'fieldname');
            $t_fieldname = '';
          }
        }
        /* if we have no fieldname here, either the prop doesn't exist, or the name changed */
        if (empty($t_fieldname)) {
          /* since the property could have been named anything by the site admin */
          /* we fetch the whole roles dd object for this user */
          /* this is a one time deal, we store this for future use throughout the module */
          $object = xarModAPIFunc('dynamicdata', 'user', 'getitem',
            array(
              'module' => 'roles', 
              'itemtype' => 0, 
              'itemid' => xarUserGetVar('uid'), 
              'getobject' => true
            ));
          /* having got the object, we look for the twitterscreenname property id */
          if (!empty($object)) {
            $properties = &$object->getProperties();
            if (!empty($properties)) {
              foreach ($properties as $key => $val) {
                // if it's not our screenname type, we can skip it
                if ($properties[$key]->type != 991991) continue;
                // found it, get what we need and get out of here
                $t_fieldname = $properties[$key]->name;
                $user_screen_name = $properties[$key]->value;
                break;
              }
            }
          }
          /* if we found a new fieldname, we store it now */
          if (!empty($t_fieldname)) {
            xarModSetVar('twitter', 'fieldname', $t_fieldname);
          } 
        }

        /* see if we got a username,password combo from dd */
        /* this is accessed by functions using (essentially) xarUserGetVar(xarModGetVar('twitter', 'fieldname')) */
        if (!empty($user_screen_name) && strpos($user_screen_name, ',') !== false) {
          list ($user_screen_name, $user_screen_pass) = explode(',',$user_screen_name);
          if (!empty($user_screen_name) && !empty($user_screen_pass)) $associated = true;
        }
        /* if we didn't get screen_name from the dd prop, see if we have a session var */
        if (empty($user_screen_name)) {
          $user_screen_name = xarSessionGetVar('twitter_screen_name');
          /* we flag here that we got from input */
          if (!empty($user_screen_name)) $rememberme = true;
        }
        /* see if we got a password from input */
        if (!empty($user_screen_name) && empty($user_screen_pass)) {
          $user_screen_pass = xarSessionGetVar('twitter_screen_pass');
          if (!empty($user_screen_pass)) $rememberme = true;
        }
        /* if we found a screen name for this user */
        /* we look up the twitter info for this user, for use by our GUI functions */
        /* basically if user_account is empty, the user didn't enter a name in the dd prop */
        /* or the prop simply doesn't exist. Either way, no account info is shown unless we got a user_account */
        if (!empty($user_screen_name)) {
          /* if we didn't get a password, we call user show method */
          /* this means the user is claiming the account is theirs, but hasn't provided a password */
          /* in this case, the user display function for this profile is displayed */
          /* the user claiming the account will be presented with a password box */
          /* entering a correct password will redirect user to the account function */
          /* password will be stored in session cache until they enter it in the dd prop */
          if (empty($user_screen_pass)) {
            $user_account = xarModAPIFunc('twitter', 'user', 'rest_methods',
              array(
                'area' => 'users',
                'method' => 'show',
                'username' => $user_screen_name, 
                'cached' => true,
                'refresh' => 3600, // user is currently active in this module, so we refresh cache frequently (1 min)
                'superrors' => true
              )); 
          /* otherwise we validate credentials for this user */
          /* this means we got a name and password */
          /* in this case the user account function will be displayed to the user when viewing this profile */
          /* user display function will be shown to all other users when viewing this profile */
          } else {
            $user_account = xarModAPIFunc('twitter', 'user', 'rest_methods',
              array(
                'area' => 'account',
                'method' => 'verify_credentials',
                'username' => $user_screen_name, 
                'password' => $user_screen_pass,
                'cached' => true,
                'refresh' => 300, // user is currently active in this module, so we refresh cache frequently (1 min)
                'superrors' => true
              ));
            // this user account didn't validate, reset everything
            if (!$user_account) {
              $associated = false;
              $rememberme = false;
              xarSessionDelVar('twitter_screen_name');
              xarSessionDelVar('twitter_screen_pass');
            } else {
              // valid account
              if ($associated) {
                // if this is an associated account, we don't need these
                xarSessionDelVar('twitter_screen_name');
                xarSessionDelVar('twitter_screen_pass');
              }
            }
          }
        } 
      }
    }

    /* if we found a user account, we get the rate status limit for the current user */
    if (!empty($user_account)) {
      $user_status = xarModAPIFunc('twitter', 'user', 'rest_methods',
        array(
          'area' => 'account',
          'method' => 'rate_limit_status',
          'username' => $user_screen_name, 
          'password' => $user_screen_pass,
          'cache' => false,
          'refresh' => 1,
          'superrors' => true
        ));
    } else {
      /* reset session cache otherwise */
      xarSessionDelVar('twitter_screen_name');
      xarSessionDelVar('twitter_screen_pass');
    }

    /* now see where we are */
    if (empty($modtype) || empty($modfunc)) {
      list($modname, $modtype, $modfunc) = xarRequestGetInfo();
    }

    /* pass the user account info to the calling function */
    $data['user_account'] = $user_account;
    $data['user_status'] = $user_status;
    $data['user_screen_pass'] = $modfunc == 'account' && !empty($user_screen_pass) ? $user_screen_pass : '';
    $data['rememberme'] = !empty($rememberme) ? $rememberme : false;
    $data['associated'] = !empty($associated) ? $associated : false;


    /* let calling functions know the default timeline */
    $data['deftimeline'] = '';//xarModGetVar('twitter', 'deftimeline');

    /* add in the dd fieldname if we have one */
    $data['t_fieldname'] = xarModGetVar('twitter', 'fieldname');

    /* Visitors with only view privs */
    if (xarSecurityCheck('ViewTwitter', 0) && xarModGetVar('twitter', 'public_timeline')) {
      /* show public timeline if allowed */
        $menu_links[] = array(
          'url' => xarModURL('twitter', 'user', 'main', array('timeline' => 'public_timeline')),
          'label' => xarML('Public Timeline'),
          'title' => xarML('View Twitter\'s public timeline'),
          'active' => ($modfunc == 'main' && $modtype == 'user') ? true : false
          );
    }
    /* Visitors with read privs */
    if (xarSecurityCheck('ReadTwitter', 0)) {
      /* have we got a site account to display timelines for ? */
      if (!empty($data['site_account']) && (xarModGetVar('twitter', 'account_display') || $data['isowner'])) {
        $urlparam = $data['isowner'] ? 'account' : 'display';
        $menu_links[] = array(
          'url' => xarModURL('twitter', 'user', $urlparam, array('screen_name' => $data['site_account']['screen_name'])),
          'label' => $data['site_account']['screen_name'],
          'title' => xarML('View timeline\'s'),
          'active' => ($modfunc == 'account' && !empty($screen_name) && $screen_name == $data['site_account']['screen_name']) ? true : false
        );
      }
    }

    /* Comment link */
    if (xarSecurityCheck('CommentTwitter', 0) && empty($data['user_account'])) {
        $menu_links[] = array(
          'url' => xarModURL('twitter', 'user', 'tweet'),
          'label' => xarML('New Tweet'),
          'title' => xarML('Update your twitter account status'),
          'active' => $modtype == 'user' && $modfunc == 'tweet' ? true : false
        );
    }
    /* View associated accounts */
    if (xarSecurityCheck('ReadTwitter', 0)) {
      if (!empty($data['t_fieldname']) && xarModGetVar('twitter', 'users_display')) {
        $menu_links[] = array(
          'url' => xarModURL('twitter', 'user', 'view'),
          'label' => xarML('Users'),
          'title' => xarML('View associated user accounts'),
          'active' => $modtype == 'user' && $modfunc == 'view' ? true : false
        );
      }
    }
    /* associated account */
    if (xarSecurityCheck('EditTwitter', 0)) {
      if (!empty($data['user_account'])) {
        if ($associated) {
          $editurl = xarModURL('twitter', 'user', 'account', array('screen_name' => $data['user_account']['screen_name']));
          $tabactive = $modtype == 'user' && $modfunc == 'account' ? true : false;
        } else {
          $editurl = xarModURL('twitter', 'user', 'display', array('screen_name' => $data['user_account']['screen_name']));
          $tabactive = $modtype == 'user' && $modfunc == 'display' ? true : false;
        }
        $menu_links[] = array(
          'url' => $editurl,
          'label' => $data['user_account']['screen_name'],
          'title' => xarML('Manage your twitter account'),
          'active' => $tabactive
        );
      }
    }

    if (xarSecurityCheck('AdminTwitter', 0)) {
      $menu_links[] = array(
        'url' => xarModURL('twitter', 'admin', 'modifyconfig'),
        'label' => xarML('Modify Config'),
        'title' => xarML('Modify Module Configuration'),
        'active' => $modtype == 'admin' && $modfunc == 'modifyconfig' ? true : false
      );
      if ($modtype == 'admin') {
        $menu_links[] = array(
          'url' => xarModURL('twitter', 'admin', 'overview'),
          'label' => xarML('Overview'),
          'title' => xarML('Module Overview'),
          'active' => $modtype == 'admin' && ($modfunc == 'main' || $modfunc == 'overview') ? true : false
        );
      }
    }

    $data['usermenu'] = $menu_links;

    return $data;
} 
?>