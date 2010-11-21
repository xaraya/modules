<?php
function twitter_restapi_list_members($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'list members');
        return $response;
    }
        
    $path = array();
    $params = array();  
            
    switch ($method) {
        
        case 'show':
            if (!isset($user) || (!is_string($user) && !is_numeric($user))) {
                $invalid[] = 'user';
            } else {
                $path[] = $user;
            }
            if (!isset($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
            $path[] = 'members';
            if (isset($cursor)) {
                if (empty($cursor) || !is_numeric($cursor)) {
                    $invalid[] = 'cursor';
                } else {
                    $params['cursor'] = $cursor;
                }
            }
        break;
        
        case 'update':
            $http_method = 'post';
            if (!isset($user) || (!is_string($user) && !is_numeric($user))) {
                $invalid[] = 'user';
            } else {
                $path[] = $user;
            }
            if (!isset($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
            $path[] = 'members';
            if (!isset($id) || (!is_numeric($id) && !is_string($id))) {
                $invalid[] = 'id';
            } else {
                $params['id'] = $id;
            }       
        break;
        
        case 'destroy':
            $http_method = 'delete';
            if (!isset($user) || (!is_string($user) && !is_numeric($user))) {
                $invalid[] = 'user';
            } else {
                $path[] = $user;
            }
            if (!isset($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
            $path[] = 'members';
            if (!isset($id) || (!is_numeric($id) && !is_string($id))) {
                $invalid[] = 'id';
            } else {
                $params['id'] = $id;
            }          
        break;
        
        case 'show_id':
            if (!isset($user) || (!is_string($user) && !is_numeric($user))) {
                $invalid[] = 'user';
            } else {
                $path[] = $user;
            }
            if (!isset($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
            $path[] = 'members';
            if (!isset($id) || (!is_numeric($id) && !is_string($id))) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }          
        break;

        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'list members', $method);
            return $response;
        break;
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'list members', $method);
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