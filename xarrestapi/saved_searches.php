<?php
function twitter_restapi_saved_searches($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method)) $method = null;
    /*
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'saved searches');
        return $response;
    }
    */
        
    $path = array('saved_searches');
    $params = array();  
            
    switch ($method) {
        
        default:
        
        break;

        case 'show':
            $path[] = $method;
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
        break;
        
        case 'create':
            $path[] = $method;
            $http_method = 'post';
            if (empty($query) || !is_string($query)) {
                $invalid[] = 'query';
            } else {
                $params['query'] = $query;
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
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'saved searches', $method);
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