<?php
function twitter_restapi_timeline($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'timeline');
        return $response;
    }  

    $path = array('statuses');
    $http_method = 'get';
    $params = array();  
            
    switch ($method) {  
        
        case 'user_timeline':
            if (isset($user_id)) {
                if (empty($user_id) || !is_numeric($user_id)) {
                    $invalid[] = 'user_id';
                } else {
                    $params['user_id'] = $user_id;
                }
            }
            if (isset($screen_name)) {
                if (empty($screen_name) || !is_string($screen_name)) {
                    $invalid[] = 'screen_name';
                } else {
                    $params['screen_name'] = $screen_name;
                }
            }
            // fall through...        
        case 'friends_timeline':
        case 'mentions':
            if (isset($include_rts)) {
                $params['include_rts'] = (bool) $include_rts;
            }
            // fall through...
        case 'home_timeline':
        case 'retweeted_by_me':
        case 'retweeted_to_me':
        case 'retweets_of_me':
            if (isset($since_id)) {
                if (empty($since_id) || !is_numeric($since_id)) {
                    $invalid[] = 'since_id';
                } else {
                    $params['since_id'] = $since_id;
                }
            }            
            if (isset($max_id)) {
                if (empty($max_id) || !is_numeric($max_id)) {
                    $invalid[] = 'max_id';
                } else {
                    $params['max_id'] = $max_id;
                }
            }        
            if (isset($count)) {
                if (empty($count) || !is_numeric($count) || $count > 200) {
                    $invalid[] = 'count';
                } else {
                    $params['count'] = $count;
                }
            }
            if (isset($page)) {
                if (empty($page) || !is_numeric($page)) {
                    $invalid[] = 'page';
                } else {
                    $params['page'] = $page;
                }
            }
            // fall through...   
        case 'public_timeline':         
            if (isset($trim_user)) {
                $params['trim_user'] = (bool) $trim_user;
            }
            if (isset($include_entities)) {
                $params['include_entities'] = (bool) $include_entities;
            }
            $path[] = $method;        
        break;

        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'timeline', $method);
            return $response;
        break;            
                               
    }

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'timeline', $method);
        return $response;
    }

    if (empty($consumer_key) || empty($consumer_secret)) {
        $consumer_key = xarModVars::get('twitter', 'consumer_key');
        $consumer_secret = xarModVars::get('twitter', 'consumer_secret');    
    }
    
    if (empty($access_token) || empty($access_token_secret)) {
        $access_token = null;
        $access_token_secret = null;
    }

    $response = xarMod::apiFunc('twitter', 'rest', '_process', 
        array(
            'path' => $path,
            'params' => $params,
            'http_method' => $http_method,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'access_token' => $access_token,
            'access_token_secret' => $access_token_secret,
            'cached' => isset($cached) ? $cached : null,
            'expires' => isset($expires) ? $expires : null,
        ));
    
    return $response;
}
?>