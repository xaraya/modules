<?php
function twitter_restapi_favorite($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method)) $method = null;
    /*
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'favorite');
        return $response;
    }
    */
        
    $path = array('favorites');
    $params = array();  
            
    switch ($method) {

        default:
            if (isset($id)) {
                if (empty($id) || (!is_numeric($id) && !is_string($id))) {
                    $invalid[] = 'id';
                } else {
                    $path[] = $id;
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
        
        case 'create':
            $path[] = $method;
            $http_method = 'post';
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
        break;
        
        case 'destroy':
            $path[] = $method;
            $http_method = 'delete';
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
        break;
                
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'favorite', $method);
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