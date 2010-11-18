<?php
function twitter_restapi_block($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'block');
        return $response;
    }
        
    $path = array('blocks');
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
        
        case 'blocking':
            $path[] = $method;
            if (isset($page)) {
                if (empty($page) || !is_numeric($page)) {
                    $invalid[] = 'page';
                } else {
                    $params['page'] = $page;
                }
            }
        break;
        
        case 'blocking_ids':
            $path[] = $method;
            $path[] = 'ids';
        break;                                       
                
        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'block', $method);
            return $response;
        break;
                
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'block', $method);
        return $response;
    }
        
    if (empty($http_method))
        $http_method = 'get';

    if (empty($consumer_key) || empty($consumer_secret)) {
        $consumer_key = xarModGetVar('twitter', 'consumer_key');
        $consumer_secret = xarModGetVar('twitter', 'consumer_secret');    
    }
    
    if (empty($access_token) || empty($access_token_secret)) {
        $access_token = null;
        $access_token_secret = null;
    }

    $response = xarModAPIFunc('twitter', 'rest', '_process', 
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