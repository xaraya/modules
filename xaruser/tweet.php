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
    if (!xarSecurityCheck('CommentTwitter')) return;
    if (!xarVarFetch('phase', 'isset', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str:1:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('screen_name', 'str:1', $screen_name, '', XARVAR_NOT_REQUIRED)) return;

    // this function gets everything we need :)
    $data = xarModAPIFunc('twitter', 'user', 'menu');
 
    $invalid = array();

    // the twitter form for site account or user account is shown in display and account functions
    if ($phase == 'form') {
      if (!empty($data['user_account'])) {
        $urlparam = !empty($data['associated']) ? 'account' : 'display';
        xarResponseRedirect(xarModURL('twitter', 'user', $urlparam, array('screen_name' => $data['user_account']['screen_name'])));
      }
    }

    if ($phase == 'update') {
      if (!xarSecConfirmAuthKey()) return;
      if (!xarVarFetch('text', 'str:1', $text, '', XARVAR_NOT_REQUIRED)) return;
      if (!xarVarFetch('screen_pass', 'str:1', $screen_pass, '', XARVAR_NOT_REQUIRED)) return;
      if (!xarVarFetch('rememberme', 'checkbox', $rememberme, false, XARVAR_NOT_REQUIRED)) return;

      if (empty($text) || strlen($text) > 160) {
        $invalid['text'] = xarML('Text must be between 1 and 160 characters');
      }
      
      if (!empty($data['site_account']) && $screen_name == $data['site_account']['screen_name'] && $data['isowner']){
        $screen_pass = xarModGetVar('twitter', 'site_screen_pass');
      } elseif (!empty($data['user_account']) && $screen_name == $data['user_account']['screen_name']) {
        if (!empty($data['t_fieldname'])) {
          $ddval = xarUserGetVar($data['t_fieldname']);
          if (!empty($ddval) && strpos($ddval, ',') !== false) {
            list($screen_name, $screen_pass) = explode(',', $ddval);
          }
        }
        if (empty($screen_pass) && !empty($data['rememberme'])) {
          $screen_pass = xarSessionGetVar('twitter_screen_pass');
        }
      }

      /* oops, we seem to have a problem */
      if (empty($screen_name) || empty($screen_pass)) {
        $invalid['screen_name'] = xarML('*Unknown screen name or password');
        $invalid['screen_pass'] = '*';
      }
      if (empty($invalid)) {
        /* if we don't have a user account we need to validate this user */
        if (empty($data['user_account'])) {
          $isvalid = xarModAPIFunc('twitter', 'user', 'rest_methods',
            array(
              'area' => 'account',
              'method' => 'verify_credentials',
              'username' => $screen_name, 
              'password' => $screen_pass,
              'cache' => true,
              'refresh' => 300,
              'superrors' => true
            ));
          if (!$isvalid) {
            $invalid['screen_name'] = xarML('*Unknown screen name or password');
            $invalid['screen_pass'] = '*';
          } 
        }
        /* if we've not tripped on the validations, we're good to send the update */
        if (empty($invalid)) {
          $response = xarModAPIFunc('twitter', 'user', 'rest_methods',
            array(
              'area' => 'statuses',
              'method' => 'update',
              'username' => $screen_name,
              'password' => $screen_pass,
              'status' => $text,
              'superrors' => true
            ));
          if (!$response) {
            $invalid[] = 'post';
            xarSessionSetVar('statusmsg', xarML('Unable to update status'));
          }
        }
        /* if we're still valid here, the status was sent */
        if (empty($invalid)) {
          /* if user ticked remember me, set session vars */
          if ($rememberme) {
            xarSessionSetVar('twitter_screen_name', $screen_name);
            xarSessionSetVar('twitter_screen_pass', $screen_pass);
          }
          xarSessionSetVar('statusupdate', xarML('Your status was updated successfully'));
          if (empty($returnurl)) $returnurl = xarModURL('twitter', 'user', 'tweet');
          return xarResponseRedirect($returnurl);
        }
      }
    }
    $data['text'] = empty($text) ? '' : $text;
    $data['screen_name'] = empty($screen_name) ? '' : $screen_name;
    $data['screen_pass'] = empty($screen_pass) ? '' : $screen_pass;
    $data['invalid'] = $invalid;
    $data['authid'] = xarSecGenAuthKey('twitter');
    $data['activetab'] = 'tweet';
    $data['itemsperpage'] = xarModGetVar('twitter', 'itemsperpage');

    return $data;
}
?>