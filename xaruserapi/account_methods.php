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
 * Handle requests to Twitter API status methods
 * 
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param method string Twitter API method to call (required)
 * @param username string Twitter username to authenticate (required for some methods)
 * @param password string password to use for authentication (required for some methods)
 * @param cached bool optionally cache GET request methods (default true) and
 * @param refresh int optionally specify time to cache files (default 300 = 5 minutes) and
 * @param cachedir str optionally specify the var subdirectory to store cached files in (default cache) and
 * @param extension str optionally specify the file extension to use for cached files (default xml) and
 * @param superrors bool optionally suppress errors accessing urls (default true - set false for debugging)
 * @return mixed array containing the items, bool true on success or bool false on failure
 */
function twitter_userapi_account_methods($args)
{ 

  extract($args);
  
  $uri = 'http://twitter.com';
  $format = 'xml';
  $path = '';
  $get = array();
  $post = array();
  $cached = !isset($cached) ? true : false;
  $refresh = empty($refresh) ? 300 : $refresh;
  $cachedir = empty($cachedir) ? 'cache' : $cachedir;
  $extension = empty($extension) ? '.xml' : $extension;
  $superrors = empty($superrors) ? true : $superrors;
  $numitems = empty($numitems) ? 20 : $numitems;
  
  // account methods always require these
  if (empty($username) || empty($password)) return;

  if (empty($method)) return;
  switch ($method) {
    case 'verify_credentials':
      $output = 'user_element'; // nfi?
    break;
    case 'end_session': 
      $post = (bool) true;
      $cache = false;
    break;
    case 'update_delivery_device':
      if (empty($device) || !ereg("^sms|im|none", $device)) return;
      $post['device'] = urlencode($device);
      $output = 'basic_user_information';
      $cache = false;
    break;

  }
  
  $url = $uri.'/account/'.$method.$path.'.'.$format;
  if (empty($post)) {
    $params = array();
    foreach ($get as $k => $v) {
      $params[] = $k.'='.$v;
    }
    if (!empty($params)) $url .= '?'.join(',', $params);
    $response = xarModAPIFunc('twitter', 'user', 'process', 
      array(
        'url' => $url,
        'cached' => $cached,
        'refresh' => $refresh,
        'cachedir' => $cachedir,
        'extension' => $extension,
        'superrors' => $superrors,
        'username' => !empty($username) ? $username : null,
        'password' => !empty($password) ? $password : null
      ));
   if (!$response) return;
  } else {
    if ($post !== true) {
      $postargs = array();
      foreach ($post as $k => $v) {
        $postargs[] = $k.'='.$v;
      }
    } else {
      $postargs = $post;
    }
    $postargs = join(',', $postargs);
    $response = xarModAPIFunc('twitter', 'user', 'process', 
      array(
        'url' => $url,
        'postargs' => $postargs,
        'cached' => false,
        'username' => $username,
        'password' => $password
      ));
    if (!$response) return;
    return true;
  }

  if (class_exists('SimpleXMLElement')){
    $xml = new SimpleXMLElement($response);
    $items = array();
    if ($xml) {
      foreach ($xml as $key => $value) {
          $items[$key] = $value;
      }
    }
    return ($items);
  } else {
    include_once('modules/base/xarclass/xmlParser.php');
    // Create a need feedParser object
    $p = new feedParser();
    // Tell feedParser to parse the data
    $xml = $p->parseFeed($response);
    // TODO:

  }
  return ($response);
}
?>