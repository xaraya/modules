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
 * @param cached bool optionally cache get request methods (default true) and
 * @param refresh int optionally specify time to cache files (default 300 = 5 minutes) and
 * @param cachedir str optionally specify the var subdirectory to store cached files in (default cache) and
 * @param extension str optionally specify the file extension od cached files (default xml) and
 * @param superrors bool optionally suppress errors accessing urls (default false)
 * @return mixed array containing the items, bool true on success or bool false on failure
 */
function twitter_userapi_status_methods($args)
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
  $extension = empty($extension) ? 'xml' : $extension;
  $superrors = empty($superrors) ? true : $superrors;
  $numitems = empty($numitems) ? 20 : $numitems;

  if (empty($method)) return;
  switch ($method) {
    case 'public_timeline':
    default:
      $output = 'status_elements';
      $cache = true;
      $refresh = 60;
    break;
    case 'friends_timeline':
      if (empty($username) || empty($password)) return;
      if (!empty($since)) {
        $get['since'] = urlencode($since);
      }
      if (!empty($since_id)) {
        $get['since_id'] = urlencode($since_id);
      }
      if (!empty($count)) {
        $get['count'] = urlencode($count);
      }
      if (!empty($page)) {
        $get['page'] = urlencode($page);
      }
      $output = 'status_elements';
    break;
    case 'user_timeline':
      if (empty($username) || empty($password)) return;
      if (!empty($user_id)) {
        $path .= '/'.urlencode($user_id);
      }
      if (!empty($since)) {
        $get['since'] = urlencode($since);
      }
      if (!empty($since_id)) {
        $get['since_id'] = urlencode($since_id);
      }
      if (!empty($count)) {
        $get['count'] = urlencode($count);
      }
      if (!empty($page)) {
        $get['page'] = urlencode($page);
      }
      $output = 'status_elements';
    break;
    case 'show':
      if (!empty($status_id)) {
        $path .= '/'.urlencode($status_id);
      }
      $output = 'status_element';
    break;
    case 'update':
      if (empty($username) || empty($password) || empty($status)) return;
      $post['status'] = urlencode($status);
      $output = 'status_element';
    break;
    case 'replies':
      if (empty($username) || empty($password) || empty($status)) return;
      if (!empty($since)) {
        $get['since'] = urlencode($since);
      }
      if (!empty($since_id)) {
        $get['since_id'] = urlencode($since_id);
      }
      if (!empty($page)) {
        $get['page'] = urlencode($page);
      }
      $output = 'status_elements';
    break;
    case 'destroy':
      if (!empty($status_id)) {
        $path .= '/'.urlencode($status_id);
      }    
      $post = (bool) true;
      $output = 'status_element';
    break;
  }
  
  $url = $uri.'/statuses/'.$method.$path.'.'.$format;
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
      $i = 0;
      foreach ($xml->status as $tweet) {
        $thistext = $tweet->text;
        // urls
        $thistext = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" rel=\"external\">\\2</a>", $thistext); 
        // hashtags
        $thistext = preg_replace("/(?:^|\W)\#([a-zA-Z0-9\-_\.+:=]+\w)(?:\W|$)/is", " <a href=\"http://hashtags.org/tag/\\1/messages\">#\\1</a> ", $thistext);
        // at tags
        $thistext = preg_replace("/(?:^|\W|#)@(\w+)/is", " <a href=\"http://twitter.com/\\1\">@\\1</a> ", $thistext);
        $items[$i] = array(
          'created_at' => strtotime($tweet->created_at),
          'screen_name' => $tweet->user->screen_name,          
          'name' => $tweet->user->name,
          'profile_image_url' => $tweet->user->profile_image_url,
          'text' => $thistext,
          'id' => $tweet->id,
          'source' => $tweet->source
        );
        $i++;
        if ($i == $numitems-1) break;
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