<?php
/**
 * Display an item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Display an item
 *
 * This is a standard function to provide detailed information on a single item
 * available from the module.
 *
 * @author the Example module development team
 * @param  array $args an array of arguments (if called by other modules)
 * @param  int $args['objectid'] a generic object id (if called by other modules)
 * @param  int $args['exid'] the item id used for this example module
 * @return array $data The array that contains all data for the template
 */
include_once('modules/twitter/xarclass/twitterAPI.php');
function twitter_user_tweet($args)
{
    if (!xarSecurityCheck('AddTwitter')) return;
    if (!xarVarFetch('phase', 'isset', $phase, 'form', XARVAR_NOT_REQUIRED)) return;

    $data = array();
    $invalid = array();
    $owner = xarModGetVar('twitter', 'owner');
    $uid = xarUserGetVar('uid');
    $isowner = $owner == $uid ? true : false;
    switch($phase) {
      case 'form':
      default:
        $data['isowner'] = $isowner;
        $data['text'] = '';
      break;
      case 'update':
        if (!xarVarFetch('text', 'str:1', $text, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('username', 'str:1', $username, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('password', 'str:1', $password, '', XARVAR_NOT_REQUIRED)) return;
        
        if (!xarSecConfirmAuthKey()) return;
        if (!$isowner) {
          if (empty($username)) $invalid[] = 'username';
          if (empty($password)) $invalid[] = 'password';
        } else {
          $username = xarModGetVar('twitter', 'username');
          $password = xarModGetVar('twitter', 'password');
        }
        if (empty($text)) $invalid['text'] = 'text';
          if (empty($invalid)) {
          $t = new twitter();
          $t->username = $username;
          $t->password = $password;
          $result = $t->update($text);  
          if ($result === false) {
            $invalid['update'] = 'update';
          }
        } 
        if (empty($invalid)) {
          xarSessionSetVar('statusmsg', xarML('Your tweet was sent succesfully'));
          xarResponseRedirect(xarModURL('twitter','user', 'tweet'));
          return true;
        } 
        $data['text'] = $text;
        $data['password'] = $password;
        $data['username'] = $username;
      break;
    }
    $data['invalid'] = $invalid;
    $data['authid'] = xarSecGenAuthKey('twitter');

    return $data;
}
?>