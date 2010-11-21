<?php
function twitter_restapi_trends($args)
{
    extract($args);
    
    $invalid = array();
    /*
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'trends');
        return $response;
    }
    */
        
    $path = array('trends');
    $params = array();  
            
    switch ($method) {
        
        default:
        
        break;
        
        case 'available':
            $path[] = $method;
            if (isset($lat)) {
                if (!isset($long) || !is_numeric($lat) || $lat > 90 || $lat < -90) {
                    $invalid[] = 'lat';
                } else {
                    $params['lat'] = $lat;
                }
            }
            if (isset($long)) {
                if (!isset($lat) || !is_numeric($long) || $long > 180 || $long < -180) {
                    $invalid[] = 'long';
                } else {
                    $params['long'] = $long;
                }
            }
        break;
                    
        case 'location':
            if (empty($woeid) || !is_numeric($woeid)) {
                $invalid[] = 'woeid';
            } else {
                $path[] = $woeid;
            }
        break;
        
        case 'current':
            $path[] = $method;
            if (isset($exclude)) {
                $params['exclude'] = 'hashtags';
            }
        break;
        
        case 'daily':
        case 'weekly':
            $path[] = $method;                     
            if (isset($date)) {
                if (is_numeric($date))
                    $date = date("Y-m-d", $date);
                if (empty($date) || !is_string($date)) {
                    $invalid[] = 'date';
                } else {
                    $params['date'] = $date;
                }
            }
            if (isset($exclude)) {
                $params['exclude'] = 'hashtags';
            }
        break;

    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'trends', $method);
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