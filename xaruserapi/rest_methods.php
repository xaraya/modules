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
function twitter_userapi_rest_methods($args)
{ 

  // no sec checks here, GUI handles module privs, request credentials handle twitter privs
  extract($args);
  
  /* set some defaults for this process */
  $uri = 'http://twitter.com';  // all methods go to this uri
  $format = '.xml';              // request format, one of json|xml for post json|xml|rss for get
  $username     = !empty($username)   ? $username   : '';
  $password     = !empty($password)   ? $password   : '';

  /* set some defaults for caching the request */
  $cached       = isset($cached)      ? $cached     : false;
  $refresh      = !empty($refresh)    ? $refresh    : 300;
  $cachedir     = !empty($cachedir)   ? $cachedir   : 'cache';
  $extension    = !empty($extension)  ? $extension  : '.xml';
  $superrors    = !empty($superrors)  ? $superrors  : false;
  
  /* set some defaults for the items to return */
  $startnum     = !empty($startnum)   ? $startnum   : 1;
  $numitems     = !empty($numitems)   ? $numitems   : 20;

  if (empty($area) || empty($method)) return false;

  $path = '';
  $get = array();
  $post = array();

  $return_method = '';
  switch ($area) {
    /* user methods */
    case 'users':
      switch ($method) {
        /** show a single user profile 
         *  returns an array of user info 
        **/
        case 'show':
          if (empty($username)) return false;
          $path .= '/'.urlencode($username);
          $return_method = 'extended_user_information';
        break;
        default:
          $unknown = true;
        break;
      }
    break;
    /* account methods */
    case 'account':
      if (empty($username) || empty($password)) return false;
      switch ($method) {
        /** verify credentials 
         *  returns an array of user information 
        **/
        case 'verify_credentials';
          $return_method = 'extended_user_information';
        break;
        /** end session
         *  returns an array of user information 
        **/
        case 'end_session':
          $post = (bool) true;
        break;
        /** update delivery device
         *  returns an array of user information
        **/
        case 'update_delivery_device':
          if (empty($device) || !ereg("^sms|im|none", $device)) return;
          $post['device'] = urlencode($device);
          $return_method = 'extended_user_information';
        break;
        /** rate limit status 
         *  returns an array of current rate status elements 
        **/
        case 'rate_limit_status':
          $return_method = 'rate_status';
        break;
        default:
          $unknown = true;
        break;
      }
    break;
    /* status methods */
    case 'statuses':
      switch ($method) {
        /** public timeline 
         *  returns an array of status elements 
        **/
        case 'public_timeline':
        default:
          $return_method = 'status_elements';
          $cached = true;
          $refresh = 60;
          if (!empty($count)) {
            $get['count'] = urlencode($count);
          }
        break;
        /** public timeline 
         *  returns an array of status elements 
        **/
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
          $return_method = 'status_elements';
        break;
        /** user timeline 
         *  returns an array of status elements 
        **/
        case 'user_timeline':
          if (empty($username) && empty($user_id)) return;
          if (empty($password) && !empty($username) && empty($user_id)) $user_id = $username;
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
          $return_method = 'status_elements';
        break;
        /** show a status 
         *  returns a status element array 
        **/
        case 'show':
          if (!empty($status_id)) {
            $path .= '/'.urlencode($status_id);
          }
          $return_method = 'status_element';
        break;
        /** update status 
         *  returns a status element array 
        **/
        case 'update':
          if (empty($username) || empty($password) || empty($status)) return;
          $post['status'] = urlencode($status);
        break;
        /** @replies  
         *  returns bool true on success
        **/
        case 'replies':
          if (empty($username) || empty($password)) return;
          if (!empty($since)) {
            $get['since'] = urlencode($since);
          }
          if (!empty($since_id)) {
            $get['since_id'] = urlencode($since_id);
          }
          if (!empty($page)) {
            $get['page'] = urlencode($page);
          }
          $return_method = 'status_elements';
        break;
        /** destroy a status 
         *  returns bool true on success 
        **/
        case 'destroy':
          if (!empty($status_id)) {
            $path .= '/'.urlencode($status_id);
          }    
          $post = (bool) true;
        break;
        /** friends and followers 
         *  returns an array of user elements 
        **/
        case 'friends':
        case 'followers': 
          if (empty($username)) return false;
          $path .= '/'.$username;
          if (!empty($page)) {
            $get['page'] = urlencode($page);
          }
          $return_method = 'basic_users_information'; 
        break;
        default:
          $unknown = true;
        break;
      }
    break;
    case 'direct_messages':
      if (empty($username) || empty($password)) return false;
      switch ($method) {
        case 'direct_messages':
        case 'sent':
          if (!empty($since)) {
            $get['since'] = urlencode($since);
          }
          if (!empty($since_id)) {
            $get['since_id'] = urlencode($since_id);
          }
          if (!empty($page)) {
            $get['page'] = urlencode($page);
          }
          $return_method = 'direct_message_elements';
        break;
        case 'new':

        break;
        case 'destroy':

        break;
        default:
          $unknown = true;
        break;
    }
    break;
    case 'favorites':
      switch ($method) {
        case 'favorites':
          if (empty($username) && empty($user_id)) return;
          if (empty($password) && !empty($username) && empty($user_id)) $user_id = $username;
          if (!empty($user_id)) {
            $path .= '/'.urlencode($user_id);
          }
          if (!empty($page)) {
            $get['page'] = urlencode($page);
          }
        $return_method = 'status_elements';
        break;
        default:
          $unknown = true;
        break;
      }
    break;
    default:
      $unknown = true;
    break;
  }

  /* unknown area or method */
  if (!empty($unknown)) return false;

  if ($area == $method) {
    $url = $uri . '/' . $area . $path . $format;
  } else {
    // this is the path to the twitter api method 
    $url = $uri . '/' . $area . '/' . $method . $path . $format;
  }
  $postargs = array();
  $params = array();
  /* get params are added after the path as ?param=value&foo=bar */
  if (empty($post) && !empty($get)) {
    foreach ($get as $k => $v) {
      $params[] = $k.'='.$v;
    }
  /* postargs are formatted as param=value,bar=foo */
  } elseif (!empty($post)) {
    $cached = false;
    if ($post !== true) {
      $postargs = array();
      foreach ($post as $k => $v) {
        $postargs[] = $k.'='.$v;
      }
      $postargs = join(',', $postargs);
    } else {
      $postargs = $post;
    }
  }
  /* add any get args to the url path */
  if (!empty($params)) $url .= '?'.join('&', $params);
  //print_r($url);
  /* now we pass the params to the process function, which fetches us the raw request response */
  $response = xarModAPIFunc('twitter', 'user', 'process', 
    array(
      'url' => $url,
      'postargs' => !empty($postargs) ? $postargs : null,
      'cached' => $cached,
      'refresh' => $refresh,
      'cachedir' => $cachedir,
      'extension' => $extension,
      'superrors' => $superrors,
      'username' => !empty($username) ? $username : null,
      'password' => !empty($password) ? $password : null
    ));
  
  /* if the response was false we can bail here */
  if (!$response) return false;

  /* if this was a post we return true here */
  if (!empty($postargs)) return true;

  /* if we got a return method we parse the response */
  if (!empty($return_method)) {
    if (class_exists('SimpleXMLElement')){
      $xml = new SimpleXMLElement($response);
    } else {
      include_once('modules/base/xarclass/xmlParser.php');
      // Create a need feedParser object
      $p = new feedParser();
      // Tell feedParser to parse the data
      $xml = $p->parseFeed($response);
      // TODO:

    }
    if (!$xml) return $response;
  /* no return method, just return the response */
  } else {
    return $response;
  }
  
  /* if we got here, we have a return method, a response and some xml to parse */
  switch ($return_method) {
    /* the extended user information contains all user info as key = value pairs */
    /* with the exception of status which is a nested array of key = value pairs */
    /* array of user information for one user */
    case 'extended_user_information':
      $items = array();
      foreach ($xml as $key => $item) {
        if ($key == 'status') {
          foreach ($item as $k => $v) {
            if ($k == 'text') {
              $value = twitter_userapi_transform($v);
            } elseif ($k == 'created_at') {
               $value = strtotime($v);
            } else {
              $value = $v;
            }
            $items[$key][$k] = $value;
          }
        } else {
          $items[$key] = $item;
        }
      }
      return $items;
    break;
    /* array of users */
    case 'basic_users_information':
      $items = array();
      if (!empty($xml->user)) {
        foreach ($xml->user as $uinfo) {
          $item = array();
          foreach ($uinfo as $key => $vals) {
            if ($key == 'status') {
              foreach ($vals as $k => $v) {
                if ($k == 'text') {
                  $value = twitter_userapi_transform($v);
                } elseif ($k == 'created_at') {
                  $value = strtotime($v);
                }else {
                  $value = $v;
                }
                $item[$key][$k] = $value;
              }
            } else {
              $item[$key] = $vals;
            }
          }
          $items[] = $item;
        }
      }
      return $items;
    break;
    /* one status element */
    case 'status_element':
      $item = array();
      foreach ($xml as $key => $vals) {
        if ($key == 'user') {
          foreach ($vals as $k => $v) {
            $item[$key][$k] = $v;
          }
        } elseif ($key == 'text') {
          $item[$key] = twitter_userapi_transform($vals);
        } elseif ($key == 'created_at') {
          $item[$key] = strtotime($vals);
        } else {
          $item[$key] = $vals;
        }
      }
      return $item;
    break;
    /* array of status elements */
    case 'status_elements':
      $items = array();
      if (!empty($xml->status)) {
        foreach ($xml->status as $status) {
          $item = array();
          foreach ($status as $key => $vals) {
            if ($key == 'user') {
              foreach ($vals as $k => $v) {
                $item[$key][$k] = $v;
              }
            } elseif ($key == 'text') {
              $item[$key] = twitter_userapi_transform($vals);
            } elseif ($key == 'created_at') {
              $item[$key] = strtotime($vals);
            }else {
              $item[$key] = $vals;
            }
          }
          $items[] = $item;
        }
      }
      return $items;
    break;
    case 'direct_message_elements':
      $items = array();
      if (!empty($xml->direct_message)) {
        foreach($xml->direct_message as $msg) {
          $item = array();
          foreach ($msg as $key => $vals) {
            if ($key == 'sender' || $key == 'recipient') {
              foreach ($vals as $k => $v) {
                $item[$key][$k] = $v;
              }
            } elseif ($key == 'text') {
              $item[$key] = twitter_userapi_transform($vals);
            } elseif ($key == 'created_at') {
              $item[$key] = strtotime($vals);
            } else {
              $item[$key] = $vals;
            }
          }
          $items[] = $item;
        }
      }
      return $items;
    break;
    /* rate limit status */
    case 'rate_status':
      $items = array();
      foreach ($xml as $key => $value) {
        $items[$key] = $value;
      }
      return ($items);
    break;
  }

  /* if we got here, we didn't find a return method, so we just return the raw response */
  return $response;
}

function twitter_userapi_transform ($text = '') {

  if (empty($text)) return '';

  // urls
  $text = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" rel=\"external\">\\2</a>", $text); 
  // hashtags
  $text = preg_replace("/(?:^|\W)\#([a-zA-Z0-9\-_\.+:=]+\w)(?:\W|$)/is", " <a href=\"http://hashtags.org/tag/\\1/messages\">#\\1</a> ", $text);
  // at tags
  $text = preg_replace("/(?:^|\W|#)@(\w+)/is", " <a href=\"http://twitter.com/\\1\">@\\1</a> ", $text);

  return $text;
}

?>