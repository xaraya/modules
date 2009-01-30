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

    switch ($phase) {
      case 'form':
      default:
        /* Specify some values for display */
        $data['username'] = xarModGetVar('twitter', 'username');
        $data['password'] = xarModGetVar('twitter', 'password');
        $data['owner']    = xarModGetVar('twitter', 'owner');
        $data['shorturls'] = xarModGetVar('twitter', 'SupportShortURLs');
        $data['usealias'] = xarModGetVar('twitter', 'useModuleAlias');
        $data['aliasname']= xarModGetVar('twitter','aliasname');
        $data['itemsperpage'] = xarModGetVar('twitter', 'itemsperpage');
        $data['showpublic'] = xarModGetVar('twitter', 'showpublic');
        $data['showuser'] = xarModGetVar('twitter', 'showuser');
        $data['showfriends'] = xarModGetVar('twitter', 'showfriends');
        $data['deftimeline'] = xarModGetVar('twitter', 'deftimeline');
      break;
      case 'update':
        if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('usealias', 'checkbox', $usealias, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('aliasname', 'str:1', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
      
        if (!xarVarFetch('username', 'str:1', $username, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('password', 'str:1', $password, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('owner', 'id', $owner, xarModGetVar('roles', 'admin'), XARVAR_NOT_REQUIRED)) return;

        if (!xarVarFetch('itemsperpage', 'int:1', $itemsperpage, 20, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showpublic', 'checkbox', $showpublic, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showuser', 'checkbox', $showuser, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showfriends', 'checkbox', $showfriends, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('deftimeline', 'enum:public:user:friends', $deftimeline, 'public', XARVAR_NOT_REQUIRED)) return;

        if (!xarSecConfirmAuthKey()) return;
        $modname = 'twitter';
        $invalid = array();
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
        
        // TODO: validations
        if ((empty($username) || empty($password))) {
          if ($deftimeline != 'public') {
            $invalid['deftimeline'] = xarML('No account specified for this timeline');
          }
          if ($showuser) {
            $invalid['showuser'] = xarML('No account specified for this timeline');
          }
          if ($showfriends) {
            $invalid['showfriends'] = xarML('No account specified for this timeline');
          }
        }
        if (!empty($username) && empty($password)) {
          $invalid['password'] = xarML('A password is required to access your Twitter account');
        }
        if ($deftimeline == 'public' && !$showpublic) {
          //$invalid['deftimeline'] = xarML('Public timeline is not enabled*');
          $invalid['showpublic'] = xarML('Selected as default but not enabled');
        } elseif ($deftimeline == 'user' && !$showuser) {
          //$invalid['deftimeline'] = xarML('User timeline is not enabled*');
          $invalid['showuser'] = xarML('Selected as default but not enabled');
        } elseif ($deftimeline == 'friends' && !$showfriends) {
          //$invalid['deftimeline'] = xarML('Friends timeline is not enabled*');
          $invalid['showfriends'] = xarML('Selected as default but not enabled');
        }
        if (!$showpublic && !$showuser && !$showfriends) {
          $invalid['deftimeline'] = xarML('No timelines selected');
        }

        if (empty($invalid)) {
          xarModSetVar($modname, 'useModuleAlias', $usealias);
          xarModSetVar($modname, 'aliasname', $aliasname);
          xarModSetVar($modname, 'SupportShortURLs', $shorturls);

          xarModSetVar($modname, 'username', $username);
          xarModSetVar($modname, 'password', $password);
          xarModSetVar($modname, 'owner', $owner);

          xarModSetVar($modname, 'itemsperpage', $itemsperpage);    
          xarModSetVar($modname, 'showpublic', $showpublic);    
          xarModSetVar($modname, 'showuser', $showuser);    
          xarModSetVar($modname, 'showfriends', $showfriends);    
          xarModSetVar($modname, 'deftimeline', $deftimeline);    
          
          xarModCallHooks('module','updateconfig', $modname,
                     array('module' => $modname));
          xarSessionSetVar('statusmsg', xarML('Twitter Module Configuration Updated'));
          xarResponseRedirect(xarModURL($modname, 'admin', 'modifyconfig'));
          return true;
        }
        /* Specify some values for display */
        $data['username'] = $username;
        $data['password'] = $password;
        $data['owner']    = $owner;
        $data['shorturls'] = $shorturls;
        $data['usealias'] = $usealias;
        $data['aliasname']= $aliasname;
        $data['itemsperpage'] = $itemsperpage;
        $data['showpublic'] = $showpublic;
        $data['showuser'] = $showuser;
        $data['showfriends'] = $showfriends;
        $data['deftimeline'] = $deftimeline;
        $data['invalid'] = $invalid;
        xarSessionSetVar('statusmsg', xarML('There was a problem updating the module configuration, see below for details'));
      break;
    }

    $data['validtls'] = array();
    $data['validtls'][] = array('id' => 'public', 'name' => xarML('Public'));
    $data['validtls'][] = array('id' => 'user', 'name' => xarML('User'));
    $data['validtls'][] = array('id' => 'friends', 'name' => xarML('Friends'));

    $hooks = xarModCallHooks('module', 'modifyconfig', 'twitter',
                       array('module' => 'twitter'));
    $data['hooks'] = $hooks;
    $data['hookoutput'] = $hooks;

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>
