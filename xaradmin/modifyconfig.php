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
    
    if (!xarVarFetch('phase', 'pre:trim:lower:enum:update', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;
    
    if ($phase == 'update') {
        if (!xarSecConfirmAuthKey()) return;

        if (!xarVarFetch('useshorturls', 'checkbox', $useshorturls, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('aliasname', 'pre:trim:str:1:64', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('usealias', 'checkbox', $usealias, false, XARVAR_NOT_REQUIRED)) return;
        //if (!xarVarFetch('itemsperpage', 'int:1:100', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;

        if (strpos($aliasname, '_') === false)
            $aliasname = str_replace(' ', '_', $aliasname);
        if (empty($aliasname)) $usealias = false;
        $oldalias = xarModVars::get('twitter','aliasname');
        $hasalias = (xarModAlias::resolve($oldalias) == 'twitter');
        if ($hasalias)
            xarModAlias::delete($oldalias, 'twitter');
        if ($usealias) 
            xarModAlias::set($aliasname, 'twitter');
            
        xarModVars::set('twitter', 'SupportShortURLs', $useshorturls);
        xarModVars::set('twitter', 'aliasname', $aliasname);        
        xarModVars::set('twitter', 'useModuleAlias', $usealias);        
        //xarModVars::set('twitter', 'itemsperpage', $itemsperpage);   
    
        if (!xarVarFetch('consumer_key', 'pre:trim:str:1:', $consumer_key, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('consumer_secret', 'pre:trim:str:1:', $consumer_secret, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('access_token', 'pre:trim:str:1:', $access_token, '', XARVAR_NOT_REQUIRED)) return;  
        if (!xarVarFetch('access_token_secret', 'pre:trim:str:1:', $access_token_secret, '', XARVAR_NOT_REQUIRED)) return;  
        
        xarModVars::set('twitter', 'consumer_key', $consumer_key);
        xarModVars::set('twitter', 'consumer_secret', $consumer_secret);
        xarModVars::set('twitter', 'access_token', $access_token);
        xarModVars::set('twitter', 'access_token_secret', $access_token_secret);

        xarModCallHooks('module', 'updateconfig', 'twitter',
            array('module' => 'twitter'));
        
        $return_url = xarModURL('twitter', 'admin', 'modifyconfig');
        xarResponse::redirect($return_url);
    }
    
    $data = array();  
    $data['useshorturls'] = xarModVars::get('twitter', 'SupportShortURLs');
    $data['aliasname'] = xarModVars::get('twitter', 'aliasname');        
    $data['usealias'] = xarModVars::get('twitter', 'useModuleAlias');        
    //$data['itemsperpage'] = xarModVars::get('twitter', 'itemsperpage');

    $data['consumer_key'] = xarModVars::get('twitter', 'consumer_key');
    $data['consumer_secret'] = xarModVars::get('twitter', 'consumer_secret');
    $data['access_token'] = xarModVars::get('twitter', 'access_token');
    $data['access_token_secret'] = xarModVars::get('twitter', 'access_token_secret');

    $data['hooks'] = xarModCallHooks('module', 'modifyconfig', 'twitter',
        array('module' => 'twitter'));

    return $data;
    
    if (!xarVarFetch('tab', 'enum:module:site:users:hooks', $tab, 'module', XARVAR_NOT_REQUIRED)) return;

    $data=xarMod::apiFunc('twitter', 'user', 'menu', array('modtype' => 'admin', 'modfunc' => 'modifyconfig'));
    $modname = 'twitter';


    switch ($tab) {
     /* Module Configuration */
     case 'module':
        switch ($phase) {
          case 'form':
          default:
            $data['shorturls'] = xarModVars::get('twitter', 'SupportShortURLs');
            $data['usealias'] = xarModVars::get('twitter', 'useModuleAlias');
            $data['aliasname']= xarModVars::get('twitter','aliasname');
            $data['public_timeline'] = xarModVars::get('twitter', 'public_timeline');
            $data['account_display'] = xarModVars::get('twitter', 'account_display');
            $data['users_display'] = xarModVars::get('twitter', 'users_display');
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
            $data['main_tab'] = xarModVars::get('twitter', 'main_tab');
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
            $curalias = xarModVars::get($modname, 'aliasname');
            $hasalias = (!empty($curalias) && xarModAlias::resolve($curalias) == $modname) ? true : false;
            if ($hasalias) xarModAlias::delete($curalias, $modname);
            $aliasname = trim($aliasname);
            if (!empty($aliasname)) {
              if (strpos($aliasname, '_') === FALSE) {
                $aliasname = str_replace(' ', '_', $aliasname);
              }
              if ($usealias) {
                xarModAlias::set($aliasname, $modname);
              }
            } else {
              $usealias = false;
            }
            xarModVars::set($modname, 'useModuleAlias', $usealias);
            xarModVars::set($modname, 'aliasname', $aliasname);
            xarModVars::set($modname, 'SupportShortURLs', $shorturls);
            xarModVars::set($modname, 'public_timeline', $public_timeline);
            xarModVars::set($modname, 'account_display', $account_display);
            xarModVars::set($modname, 'users_display', $users_display);
            xarModVars::set($modname, 'main_tab', $main_tab);
            xarModCallHooks('module','updateconfig', $modname,
                         array('module' => $modname));
            xarSession::setVar('statusmsg', xarML('Module configuration updated successfully'));
            return xarResponse::redirect(xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => $tab)));
          break;
      }
      break;
      case 'site':
        /* TODO: site account owner to store an array of users? */
        switch ($phase) {
          case 'form':
          default:
            $data['username'] = xarModVars::get('twitter', 'site_screen_name');
            $data['password'] = xarModVars::get('twitter', 'site_screen_pass');
            $data['owner'] = xarModVars::get('twitter', 'site_screen_role');
          break;
          case 'update':
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('username', 'str:1', $username, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('password', 'str:1', $password, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('owner', 'id', $owner, xarModVars::get('roles', 'admin'), XARVAR_NOT_REQUIRED)) return;

            if (!empty($username) && empty($password)) {
              $invalid['password'] = xarML('A password is required to access your Twitter account');
            }
            if (!empty($username) && !empty($password)) {
              $isvalid = xarMod::apiFunc('twitter', 'user', 'rest_methods',
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
              xarModVars::set($modname, 'site_screen_name', $username);
              xarModVars::set($modname, 'site_screen_pass', $password);
              xarModVars::set($modname, 'site_screen_role', $owner);
              xarSession::setVar('statusmsg', xarML('Twitter Module Configuration Updated Successfully'));
              xarResponse::redirect(xarModURL($modname, 'admin', 'modifyconfig', array('tab' => $tab)));
              return true;
            }
            /* Specify some values for display */
            $data['username'] = $username;
            $data['password'] = $password;
            $data['owner']    = $owner;
            $data['invalid'] = $invalid;
            xarSession::setVar('statusmsg', xarML('There was a problem updating the module configuration, see below for details'));
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
            $data['user_timeline'] = xarModVars::get('twitter', 'user_timeline');
            $data['friends_display'] = xarModVars::get('twitter', 'friends_display');
            $data['profile_image'] = xarModVars::get('twitter', 'profile_image');
            $data['profile_description'] = xarModVars::get('twitter', 'profile_description');
            $data['profile_location'] = xarModVars::get('twitter', 'profile_location');
            $data['followers_count'] = xarModVars::get('twitter', 'followers_count');
            $data['friends_count'] = xarModVars::get('twitter', 'friends_count');
            $data['last_status'] = xarModVars::get('twitter', 'last_status');
            $data['profile_url'] = xarModVars::get('twitter', 'profile_url');
            $data['statuses_count'] = xarModVars::get('twitter', 'statuses_count');
            $data['favourites_display'] = xarModVars::get('twitter', 'favourites_display');
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
            xarModVars::set('twitter', 'user_timeline', $user_timeline);
            xarModVars::set('twitter', 'friends_display', $friends_display);
            xarModVars::set('twitter', 'profile_image', $profile_image);
            xarModVars::set('twitter', 'profile_description', $profile_description);
            xarModVars::set('twitter', 'profile_location', $profile_location);
            xarModVars::set('twitter', 'followers_count', $followers_count);
            xarModVars::set('twitter', 'friends_count', $friends_count);
            xarModVars::set('twitter', 'last_status', $last_status);
            xarModVars::set('twitter', 'profile_url', $profile_url);
            xarModVars::set('twitter', 'statuses_count', $statuses_count);
            xarModVars::set('twitter', 'favourites_display', $favourites_display);
            xarSession::setVar('statusmsg', xarML('Twitter Module Configuration Updated Successfully'));
            xarResponse::redirect(xarModURL($modname, 'admin', 'modifyconfig', array('tab' => $tab)));
            return true;
          break;
        }
      break;
      case 'hooks':
            if (!xarVarFetch('hookmodname', 'str:1:', $hookmodname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('hookmodtype', 'int:1', $hookmodtype, 0, XARVAR_NOT_REQUIRED)) return;

            $hookmods = array();
            $hooktypes = array();
            $hookmods[] = array(
                'label' => xarML('Defaults'),
                'url' => xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => 'hooks')),
                'title' => xarML('Default hook configuration settings'),
                'active' => empty($hookmodname) ? true : false
              );
            $callingmods = xarMod::apiFunc('modules', 'admin', 'gethookedmodules',
                array(
                    'hookModName' => 'twitter'
                )
            );
            if (!empty($callingmods)) {
                foreach ($callingmods as $cmodname => $cmodtypes) {
                    $modid = xarMod::getRegID($cmodname);
                    if (!$modid) continue;
                    $modinfo = xarMod::getInfo($modid);
                    if (empty($modinfo)) continue;
                    $hookmods[] = array(
                        'label' => $modinfo['displayname'],
                        'url' => xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => 'hooks', 'hookmodname' => $cmodname)),
                        'title' => $modinfo['description'],
                        'active' => $hookmodname == $cmodname ? true : false
                    );
                    if (!empty($cmodtypes) && !isset($cmodtypes[0]) && $hookmodname == $cmodname) {
                        $hooktypes[] = array(
                            'label' => xarML('Defaults'),
                            'url' => xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => 'hooks', 'hookmodname' => $cmodname)),
                            'title' => xarML('Defaults for #(1) module items',$modinfo['displayname']),
                            'active' => empty($hookmodtype) ? true : false
                            );
                        foreach ($cmodtypes as $cmodtype => $cmodval) {
                            $mytypes = xarMod::apiFunc($cmodname, 'user', 'getitemtypes', array(), 0);
                            $label = isset($mytypes[$cmodtype]['label']) ? $mytypes[$cmodtype]['label'] : $modinfo['displayname'].' ('.$cmodtype.')';
                            $title = isset($mytypes[$cmodtype]['title']) ? $mytypes[$cmodtype]['title'] : $modinfo['displayname'].' ('.$cmodtype.')';
                            $hooktypes[] = array(
                                'label' => $label,
                                'url' => xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => 'hooks', 'hookmodname' => $cmodname, 'hookmodtype' => $cmodtype)),
                                'title' => $cmodname.' ('.$cmodtype.')',
                                'active' => $hookmodtype == $cmodtype ? true : false
                            );
                        }
                    } elseif (isset($cmodtypes[0]) && $hookmodname == $cmodname)  {
                        $hooktypes[] = array(
                            'label' => xarML('All Items (0)'),
                            'url' => xarModURL('twitter', 'admin', 'modifyconfig', array('tab' => 'hooks', 'hookmodname' => $cmodname)),
                            'title' => xarML('All #(1) module items',$modinfo['displayname']),
                            'active' => true
                            );
                    }
                }
            }
            switch ($phase) {
                case 'form':
                    // try settings for module / itemtype we're hooked to
                    if (!empty($hookmodname) && !empty($hookmodtype)) {
                        $string = xarModVars::get('twitter', $hookmodname . '.' . $hookmodtype);
                    }
                    // fall back to default settings for module (itemtype 0) we're hooked to
                    if (!empty($hookmodname) && empty($string)) {
                        $string = xarModVars::get('twitter', $hookmodname);
                    }
                    // fall back to twitter module defaults
                    if (empty($string)) {
                        $string = xarModVars::get('twitter', 'twitter');
                    }
                    // this should never be empty
                    if (!empty($string)) {
                        $settings = unserialize($string);
                    }

                    $sendopts = array();
                    $sendopts[] = array('id' => 1, 'name' => xarML('All Users'));
                    $sendopts[] = array('id' => 2, 'name' => xarML('Owner (#(1))', xarUserGetVar('name', $data['owner'])));
                    $data['sendopts'] = $sendopts;

                    $data['settings'] = $settings;

                break;
                case 'update':
                    $settings = array();
                    if (!xarVarFetch('urltype', 'str:1:', $settings['urltype'], 'user', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('urlfunc', 'str:1:', $settings['urlfunc'], 'display', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('urlitemtype', 'str', $settings['urlitemtype'], 'itemtype', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('urlitemid', 'str:1:', $settings['urlitemid'], 'itemid', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('fieldname', 'str:1:', $settings['fieldname'], '', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('senduser', 'int', $settings['senduser'], 0, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('sendsite', 'int', $settings['sendsite'], 0, XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('urlextra', 'str:1:', $settings['urlextra'], '', XARVAR_NOT_REQUIRED)) return;
                    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

                    if (empty($hookmodname)) {
                        xarModVars::set('twitter', 'twitter', serialize($settings));
                    } elseif (empty($hookmodtype)) {
                        xarModVars::set('twitter', $hookmodname, serialize($settings));
                    } else {
                        xarModVars::set('twitter', $hookmodname . '.' . $hookmodtype, serialize($settings));
                    }
                    if (empty($returnurl)) {
                        $returnurl = xarModURL('twitter', 'admin', 'modifyconfig',
                            array(
                                'tab' => 'hooks',
                                'hookmodname' => !empty($hookmodname) ? $hookmodname : NULL,
                                'hookmodtype' => !empty($hookmodtype) ? $hookmodtype : NULL
                            ));
                    }
                    xarSession::setVar('statusmsg', xarML('Hooks configuration updated succesfully'));
                    return xarResponse::redirect($returnurl);

                break;
            }
            $data['hookmodname'] = $hookmodname;
            $data['hookmodtype'] = $hookmodtype;
            $data['hookmods'] = $hookmods;
            $data['hooktypes'] = $hooktypes;
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
