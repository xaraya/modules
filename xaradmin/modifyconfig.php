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
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return mixed array on form , bool on update
 */
function twitter_admin_modifyconfig()
{ 

    if (!xarSecurityCheck('AdminTwitter')) return;

    if (!xarVarFetch('phase', 'isset', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tab', 'enum:module:site:users', $tab, 'module', XARVAR_NOT_REQUIRED)) return;

    $data=xarModAPIFunc('twitter', 'user', 'menu', array('modtype' => 'admin', 'modfunc' => 'modifyconfig'));
    $modname = 'twitter';


    switch ($tab) {
     /* Module Configuration */
     case 'module':
        switch ($phase) {
          case 'form':
          default:
            $data['shorturls'] = xarModGetVar('twitter', 'SupportShortURLs');
            $data['usealias'] = xarModGetVar('twitter', 'useModuleAlias');
            $data['aliasname']= xarModGetVar('twitter','aliasname');
            $data['public_timeline'] = xarModGetVar('twitter', 'public_timeline');
            $data['account_display'] = xarModGetVar('twitter', 'account_display');
            $data['users_display'] = xarModGetVar('twitter', 'users_display');
            $main_tabs = array();
            if ($data['public_timeline']) {
              $main_tabs[] = array('id' => 'public_timeline', 'name' => xarML('Public Timeline'));
            }
            if (!empty($data['site_account']) && $data['account_display']) {
              $main_tabs[] = array('id' => 'account_display', 'name' => xarML('Site Account'));
            }
            $main_tabs[] = array('id' => 'new_tweet', 'name' => xarML('New Tweet'));
            if ($data['users_display'] && !empty($data['t_fieldname'])) {
              $main_tabs[] = array('id' => 'users_display', 'name' => xarML('Users'));
            }
            $data['main_tabs'] = $main_tabs;
            $data['main_tab'] = xarModGetVar('twitter', 'main_tab');
            $hooks = xarModCallHooks('module', 'modifyconfig', 'twitter',
                               array('module' => 'twitter'));
            $data['hooks'] = $hooks;
            $data['hookoutput'] = $hooks;
          break;
          case 'update':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('usealias', 'checkbox', $usealias, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('aliasname', 'str:1', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('public_timeline', 'checkbox', $public_timeline, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('account_display', 'checkbox', $account_display, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('users_display', 'checkbox', $users_display, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('main_tab', 'str:1', $main_tab, '', XARVAR_NOT_REQUIRED)) return;
            $curalias = xarModGetVar($modname, 'aliasname');
            $hasalias = (!empty($curalias) && xarModGetAlias($curalias) == $modname) ? true : false;
            if ($hasalias) xarModDelAlias($curalias, $modname);
            $aliasname = trim($aliasname);
            if (!empty($aliasname)) {
              if (strpos($aliasname, '_') === FALSE) {
                $aliasname = str_replace(' ', '_', $aliasname);
              }
              if ($usealias) {
                xarModSetAlias($aliasname, $modname);
              }
            } else {
              $usealias = false;
            }
            xarModSetVar($modname, 'useModuleAlias', $usealias);
            xarModSetVar($modname, 'aliasname', $aliasname);
            xarModSetVar($modname, 'SupportShortURLs', $shorturls);
            xarModSetVar($modname, 'public_timeline', $public_timeline);
            xarModSetVar($modname, 'account_display', $account_display);
            xarModSetVar($modname, 'users_display', $users_display);
            xarModSetVar($modname, 'main_tab', $main_tab);
            xarModCallHooks('module','updateconfig', $modname,
                         array('module' => $modname));
            xarSessionSetVar('statusmsg', xarML('Module configuration updated successfully'));
            return xarResponseRedirect(xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => $tab)));
          break;
      }
      break;
      case 'site':
        /* TODO: site account owner to store an array of users? */
        switch ($phase) {
          case 'form':
          default:
            $data['username'] = xarModGetVar('twitter', 'site_screen_name');
            $data['password'] = xarModGetVar('twitter', 'site_screen_pass');
            $data['owner'] = xarModGetVar('twitter', 'site_screen_role');
          break;
          case 'update':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('username', 'str:1', $username, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('password', 'str:1', $password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('owner', 'id', $owner, xarModGetVar('roles', 'admin'), XARVAR_NOT_REQUIRED)) return;

            if (!empty($username) && empty($password)) {
              $invalid['password'] = xarML('A password is required to access your Twitter account');
            }
            if (!empty($username) && !empty($password)) {
              $isvalid = xarModAPIFunc('twitter', 'user', 'rest_methods', 
                array(
                  'area' => 'account',
                  'method' => 'verify_credentials',
                  'username' => $username, 
                  'password' => $password,
                  'cached' => true,
                  'refresh' => 3600,
                  'superrors' => true
                ));
              if (!$isvalid) {
                $invalid['username'] = xarML('*Invalid username or password');
                $invalid['password'] = '*';
              }
            }

            if (empty($invalid)) {
              xarModSetVar($modname, 'site_screen_name', $username);
              xarModSetVar($modname, 'site_screen_pass', $password);
              xarModSetVar($modname, 'site_screen_role', $owner);
              xarSessionSetVar('statusmsg', xarML('Twitter Module Configuration Updated Successfully'));
              xarResponseRedirect(xarModURL($modname, 'admin', 'modifyconfig', array('tab' => $tab)));
              return true;
            }
            /* Specify some values for display */
            $data['username'] = $username;
            $data['password'] = $password;
            $data['owner']    = $owner;
            $data['invalid'] = $invalid;
            xarSessionSetVar('statusmsg', xarML('There was a problem updating the module configuration, see below for details'));
          break;
        }
      break;
      case 'users':
        switch ($phase) {
          case 'form':
            $displayopts = array();
            $displayopts[] = array('id' => 0, 'name' => xarML('Never'));
            $displayopts[] = array('id' => 1, 'name' => xarML('Always'));
            $displayopts[] = array('id' => 2, 'name' => xarML('Optional'));
            $data['displayopts'] = $displayopts;
            $data['user_timeline'] = xarModGetVar('twitter', 'user_timeline');
            $data['friends_display'] = xarModGetVar('twitter', 'friends_display');
            $data['profile_image'] = xarModGetVar('twitter', 'profile_image');
            $data['profile_description'] = xarModGetVar('twitter', 'profile_description');
            $data['profile_location'] = xarModGetVar('twitter', 'profile_location');
            $data['followers_count'] = xarModGetVar('twitter', 'followers_count');
            $data['friends_count'] = xarModGetVar('twitter', 'friends_count');
            $data['last_status'] = xarModGetVar('twitter', 'last_status');
            $data['profile_url'] = xarModGetVar('twitter', 'profile_url');
            $data['statuses_count'] = xarModGetVar('twitter', 'statuses_count');
            $data['favourites_display'] = xarModGetVar('twitter', 'favourites_display');
          break;
          case 'update':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('user_timeline', 'int:0', $user_timeline, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('friends_display', 'int:0', $friends_display, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('profile_image', 'int:0', $profile_image, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('profile_description', 'int:0', $profile_description, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('profile_location', 'int:0', $profile_location, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('followers_count', 'int:0', $followers_count, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('friends_count', 'int:0', $friends_count, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('last_status', 'int:0', $last_status, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('profile_url', 'int:0', $profile_url, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('statuses_count', 'int:0', $statuses_count, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('favourites_display', 'int:0', $favourites_display, 0, XARVAR_NOT_REQUIRED)) return;
            xarModSetVar('twitter', 'user_timeline', $user_timeline);
            xarModSetVar('twitter', 'friends_display', $friends_display);
            xarModSetVar('twitter', 'profile_image', $profile_image);
            xarModSetVar('twitter', 'profile_description', $profile_description);
            xarModSetVar('twitter', 'profile_location', $profile_location);
            xarModSetVar('twitter', 'followers_count', $followers_count);
            xarModSetVar('twitter', 'friends_count', $friends_count);
            xarModSetVar('twitter', 'last_status', $last_status);
            xarModSetVar('twitter', 'profile_url', $profile_url);
            xarModSetVar('twitter', 'statuses_count', $statuses_count);
            xarModSetVar('twitter', 'favourites_display', $favourites_display);
            xarSessionSetVar('statusmsg', xarML('Twitter Module Configuration Updated Successfully'));
            xarResponseRedirect(xarModURL($modname, 'admin', 'modifyconfig', array('tab' => $tab)));
            return true;
          break;
        }
      break;
    }

    $data['validtls'] = array();
    $data['validtls'][] = array('id' => 'public', 'name' => xarML('Public'));
    $data['validtls'][] = array('id' => 'user', 'name' => xarML('User'));
    $data['validtls'][] = array('id' => 'friends', 'name' => xarML('Friends'));

    $data['tab'] = $tab;

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>
