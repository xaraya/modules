<?php
function twitter_restapi_friendship($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'friendship');
        return $response;
    }
        
    $path = array('friendships');
    $params = array();  
            
    switch ($method) {
    
        case 'create':
            $path[] = $method;
            $http_method = 'post';
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
            if (isset($follow)) {
                $params['follow'] = (bool) $follow ? 'true' : null;
            }            
        break;
        
        case 'destroy':
            $path[] = $method;
            $http_method = 'delete';
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
        break;
        
        case 'exists':
            $path[] = $method;
            if (empty($user_a) || (!is_numeric($user_a) && !is_string($user_a))) {
                $invalid[] = 'user_a';
            } else {
                $params['user_a'] = $user_a;
            }            
            if (empty($user_b) || (!is_numeric($user_b) && !is_string($user_b))) {
                $invalid[] = 'user_b';
            } else {
                $params['user_b'] = $user_b;
            }
        break;
        
        case 'show':
            $path[] = $method;
            if (isset($source_id)) {
                if (empty($source_id) || !is_numeric($source_id)) {
                    $invalid[] = 'source_id';
                } else {
                    $params['source_id'] = $source_id;
                }
            } elseif (isset($source_screen_name)) {
                if (empty($source_screen_name) || !is_string($source_screen_name)) {
                    $invalid[] = 'source_screen_name';
                } else {
                    $params['source_screen_name'] = $source_screen_name;
                }
            } else {
                $invalid[] = 'source_screen_name';
            }
            if (isset($target_id)) {
                if (empty($target_id) || !is_numeric($target_id)) {
                    $invalid[] = 'target_id';
                } else {
                    $params['target_id'] = $target_id;
                }
            } elseif (isset($target_screen_name)) {
                if (empty($target_screen_name) || !is_string($target_screen_name)) {
                    $invalid[] = 'target_screen_name';
                } else {
                    $params['target_screen_name'] = $target_screen_name;
                }
            } else {
                $invalid[] = 'target_screen_name';
            }
        break;
        
        case 'incoming':
        case 'outgoing':
            $path[] = $method;
            if (isset($cursor)) {
                if (empty($cursor) || !is_numeric($cursor)) {
                    $invalid[] = 'cursor';
                } else {
                    $params['cursor'] = $cursor;
                }
            }
        break;            

        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'friendship', $method);
            return $response;
        break;
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'friendship', $method);
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