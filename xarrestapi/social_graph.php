<?php
function twitter_restapi_social_graph($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'social graph');
        return $response;
    }
        
    $path = array();
    $params = array();  
            
    switch ($method) {
    
        case 'friends':
        case 'followers':
            $path[] = $method;
            $path[] = 'ids';
            if (isset($id)) {
                if (empty($id) || (!is_numeric($id) && !is_string($id))) {
                    $invalid[] = 'id';
                } else {
                    $path[] = $id;
                }
            } elseif (isset($user_id)) {
                if (empty($user_id) || !is_numeric($user_id)) {
                    $invalid[] = 'user_id';
                } else {
                    $params['user_id'] = $user_id;
                }
            } elseif (isset($screen_name)) {
                if (empty($screen_name) || !is_string($screen_name)) {
                    $invalid[] = 'screen_name';
                } else {
                    $params['screen_name'] = $screen_name;
                }
            } else {
                $invalid[] = 'params';
            }
            if (isset($cursor)) {
                if (empty($cursor) || !is_numeric($cursor)) {
                    $invalid[] = 'cursor';
                } else {
                    $params['cursor'] = $cursor;
                }
            }
        break;

        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'social graph', $method);
            return $response;
        break;
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'social graph', $method);
        return $response;
    }
        
    if (empty($http_method))
        $http_method = 'get';

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