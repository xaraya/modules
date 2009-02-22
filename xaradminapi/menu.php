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
 * Generate admin menu
 * 
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
function twitter_adminapi_menu()
{ 
    $menu = $data = array();
    $owner = xarModGetVar('twitter', 'owner');
    $uid = xarUserGetVar('uid');
    $isowner = $uid == $owner ? true :false;
    list($modname, $modtype, $modfunc) = xarRequestGetInfo();
    $username = xarModGetVar('twitter', 'username');
    $password = !empty($username) ? xarModGetVar('twitter', 'password') : '';
    if (!empty($username) && !empty($password)) {
      $userinfo = xarModAPIFunc('twitter', 'user', 'account_methods',
        array(
          'method' => 'verify_credentials',
          'username' => $username, 
          'password' => $password,
          'cache' => true,
          'refresh' => 3600
        ));
      if (!$userinfo) {
        $userinfo = xarML('Couldn\'t validate account named #(1)', $username); 
      } else {
        $statuslimit = xarModAPIFunc('twitter', 'user', 'account_methods',
          array(
            'method' => 'rate_limit_status',
            'username' => $username, 
            'password' => $password,
            'cache' => false,
            'refresh' => 1
          ));   
      }
    }

    // modify config
    if (xarSecurityCheck('AdminTwitter', 0)) {
      $menu[] = array(
        'url' => xarModURL('twitter', 'admin', 'modifyconfig'),
        'label' => xarML('Modify Config'),
        'title' => xarML('Modify module configuration'),
        'active' => $modfunc == 'modifyconfig' ? true : false
        );
    }
    // Send site tweets
    if ($isowner && (!empty($userinfo) && is_array($userinfo))) {
      $menu[] = array(
        'url' => xarModURL('twitter', 'admin', 'account'),
        'label' => $userinfo['screen_name'],
        'title' => xarML('View and configure site account'),
        'active' => $modfunc == 'tweet' ? true : false
        );   
    }
    if (xarSecurityCheck('AddTwitter', 0)) {
      $menu[] = array(
        'url' => xarModURL('twitter', 'admin', 'overview'),
        'label' => xarML('Overview'),
        'title' => xarML('Twitter Module Overview'),
        'active' => $modfunc == 'overview' ? true : false
        ); 
    }
    $data['userinfo'] = empty($userinfo) ? xarML('No Site Account Specified') : $userinfo;
    $data['statuslimit'] = empty($statuslimit) ? '' : $statuslimit;
    $data['adminmenu'] = $menu;
    

    return $data;
} 
?>