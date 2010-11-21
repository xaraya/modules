<?php
function twitter_restapi_user($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'user');
        return $response;
    }
        
    $path = array();
    $params = array();  
            
    switch ($method) {
        
        case 'show':
            $path[] = 'users';
            $path[] = $method;
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
        
        case 'lookup':
            $path[] = 'users';
            $path[] = $method;
            if (isset($user_id)) {
                if (is_array($user_id))
                    $user_id = join(',',$user_id);
                if (empty($user_id) || !is_string($user_id)) {
                    $invalid[] = 'user_id';
                } else {
                    $params['user_id'] = $user_id;
                }
            }
            if (isset($screen_name)) {
                if (is_array($screen_name))
                    $screen_name = join(',',$screen_name);
                if (empty($screen_name) || !is_string($screen_name)) {
                    $invalid[] = 'screen_name';
                } else {
                    $params['screen_name'] = $screen_name;
                }
            }
            if (empty($params)) 
                $invalid[] = 'params';
        break;
        
        case 'search':
            $path[] = 'users';
            $path[] = $method;
            if (!isset($q) || !is_string($q)) {
                $invalid[] = 'q';
            } else {
                $params['q'] = $q;
            }
            if (isset($per_page)) {
                if (empty($per_page) || !is_numeric($per_page)) {
                    $invalid[] = 'per_page';
                } else {
                    $params['per_page'] = $per_page;
                }
            }
            if (isset($page)) {
                if (empty($page) || !is_numeric($page)) {
                    $invalid[] = 'page';
                } else {
                    $params['page'] = $page;
                }
            }
        break;
        
        case 'suggestions':
            $path[] = 'users';
            $path[] = $method;
            if (isset($slug)) {
                if (empty($slug) || !is_string($slug)) {
                    $invalid[] = 'slug';
                } else {
                    $path[] = $slug;
                }
            }
        break;
                    
        case 'friends':
        case 'followers':
            $path[] = 'statuses';
            $path[] = $method;
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
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'user', $method);
            return $response;
        break;
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'user', $method);
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