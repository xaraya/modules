<?php
function twitter_restapi_account($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'account');
        return $response;
    }
        
    $path = array('account');
    $params = array();  
            
    switch ($method) {
        case 'verify_credentials':
            $path[] = $method;
        break;
        
        case 'rate_limit_status':
            $path[] = $method;
        break;
        
        case 'end_session':
            $http_method = 'post';
            $path[] = $method;
        break;
        
        case 'update_delivery_device':
            if (!isset($device) || 
                !is_string($device) || 
                ($device != 'none' && $device != 'sms')) {
                $invalid[] = 'device';
                break;
            }
            $http_method = 'post';
            $path[] = $method;
            $params['device'] = $device;
        break;
        
        case 'update_profile_colors':
            if (isset($profile_background_color)) {
                if (!is_string($profile_background_color) ||
                    !preg_match('/^#[0-9A-Fa-f]{3,6}/', $profile_background_color)) {
                    $invalid[] = 'profile_background_color';
                } else {
                    $params['profile_background_color'] = $profile_background_color;
                }
            }
            if (isset($profile_text_color)) {
                if (!is_string($profile_text_color) ||
                    !preg_match('/^#[0-9A-Fa-f]{3,6}/', $profile_text_color)) {
                    $invalid[] = 'profile_text_color';
                } else {
                    $params['profile_text_color'] = $profile_text_color;
                }
            }
            if (isset($profile_link_color)) {
                if (!is_string($profile_link_color) ||
                    !preg_match('/^#[0-9A-Fa-f]{3,6}/', $profile_link_color)) {
                    $invalid[] = 'profile_link_color';
                } else {
                    $params['profile_link_color'] = $profile_link_color;
                }
            }
            if (isset($profile_sidebar_fill_color)) {
                if (!is_string($profile_sidebar_fill_color) ||
                    !preg_match('/^#[0-9A-Fa-f]{3,6}/', $profile_sidebar_fill_color)) {
                    $invalid[] = 'profile_sidebar_fill_color';
                } else {
                    $params['profile_sidebar_fill_color'] = $profile_sidebar_fill_color;
                }
            }
            if (isset($profile_sidebar_border_color)) {
                if (!is_string($profile_sidebar_border_color) ||
                    !preg_match('/^#[0-9A-Fa-f]{3,6}/', $profile_sidebar_border_color)) {
                    $invalid[] = 'profile_sidebar_border_color';
                } else {
                    $params['profile_sidebar_border_color'] = $profile_sidebar_border_color;
                }
            }
            if (empty($params)) 
                $invalid[] = 'params';              
            if (!empty($invalid)) break;
            $http_method = 'post';
            $path[] = $method;
        break;
        
        case 'update_profile_image':
            if (!isset($image) ||
                !is_string($image)) {
                $invalid[] = 'image';
                break;
            }
            $params['image'] = $image;
            $http_method = 'post';
            $path[] = $method;
        break;
        
        case 'update_profile_background_image':
            if (!isset($image) ||
                !is_string($image)) {
                $invalid[] = 'image';
                break;
            }
            if (isset($tile)) {
                $params['tile'] = (bool) $tile;
            }
            $params['image'] = $image;
            $http_method = 'post';
            $path[] = $method;
        break;
        
        case 'update_profile':
            if (isset($name)) {
                if (empty($name) || 
                    !is_string($name) ||
                    strlen($name) > 20) {
                    $invalid[] = 'name';
                } else {
                    $params['name'] = $name;
                }
            }
            if (isset($url)) {
                if (empty($url) ||
                    !is_string($url) ||
                    strlen($url) > 100) {
                    $invalid[] = 'url';
                } else {
                    $params['url'] = $url;
                }
            }
            if (isset($location)) {
                if (empty($location) ||
                    !is_string($location) ||
                    strlen($location) > 30) {
                    $invalid[] = 'location';
                } else {
                    $params['location'] = $location;
                }
            }          
            if (isset($description)) {
                if (empty($description) ||
                    !is_string($description) ||
                    strlen($description) > 160) {
                    $invalid[] = 'description';
                } else {
                    $params['description'] = $description;
                }
            }
            if (empty($params)) 
                $invalid[] = 'params';              
            if (!empty($invalid)) break;
            $http_method = 'post';
            $path[] = $method;
        break;
        
        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'account', $method);
            return $response;
        break;
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'account', $method);
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