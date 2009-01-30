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
 * Update Twitter status
 *
 * This is a standard function to provide detailed information on a single item
 * available from the module.
 *
 * @author the Example module development team
 * @param  array $args an array of arguments (if called by other modules)
 * @param  int $args['phase'] function phase, form (default) or update on submit
 * @param  int $args['text'] the twitter status to send
 * @return array mixed $data The array that contains all data for the template or true on update
 */
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
        $data['username'] = xarModGetVar('twitter', 'username');
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
          $response = xarModAPIFunc('twitter', 'user', 'status_methods',
            array(
              'method' => 'update',
              'username' => $username,
              'password' => $password,
              'status' => $text
            ));
          if (!$response) {
            $invalid[] = 'post';
            xarSessionSetVar('statusmsg', xarML('Unable to update status'));
          }
        } 
        if (empty($invalid)) {
          xarSessionSetVar('statusmsg', xarML('Your tweet was sent succesfully'));
          xarResponseRedirect(xarModURL('twitter','user', 'tweet'));
          return true;
        } 
        $data['text'] = $text;
        $data['username'] = $username;
      break;
    }
    $data['invalid'] = $invalid;
    $data['authid'] = xarSecGenAuthKey('twitter');
    $data['activetab'] = 'tweet';
    $data['isowner']    = $isowner;
    $data['itemsperpage'] = xarModGetVar('twitter', 'itemsperpage');
    $data['showpublic'] = xarModGetVar('twitter', 'showpublic');
    $data['showuser'] = xarModGetVar('twitter', 'showuser');
    $data['showfriends'] = xarModGetVar('twitter', 'showfriends');
    $data['deftimeline'] = xarModGetVar('twitter', 'deftimeline');
    return $data;
}
?>