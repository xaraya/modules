<?php
function twitter_restapi_status($args)
{
    extract($args);
    
    $invalid = array();
    if (empty($method) || !is_string($method)) 
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'status');
        return $response;
    }
        
    $path = array('statuses');
    $params = array();  
            
    switch ($method) {
        
        case 'destroy':
        case 'retweet':
            $http_method = 'post';
        case 'show':
            $path[] = $method;
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
            if (isset($trim_user)) {
                $params['trim_user'] = (bool) $trim_user;
            }
            if (isset($include_entities)) {
                $params['include_entities'] = (bool) $include_entities;
            }        
        break;
        
        case 'update':
            if (empty($status) || !is_string($status)) {
                $invalid[] = 'status';
            } else {
                $params['status'] = $status;
            }
            if (isset($in_reply_to_status_id)) {
                if (empty($in_reply_to_status_id) || !is_numeric($in_reply_to_status_id)) {
                    $invalid[] = 'in_reply_to_status_id';
                } else {
                    $params['in_reply_to_status_id'] = $in_reply_to_status_id;
                }
            }
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
            if (isset($place_id)) {
                if (empty($place_id) || !is_string($place_id)) {
                    $invalid[] = 'place_id';
                } else {
                    $params['place_id'] = $place_id;
                }
            }
            if (isset($display_coordinates)) {
                $params['display_coordinates'] = ($display_coordinates) ? 'true' : '';
            }
            if (isset($trim_user)) {
                $params['trim_user'] = (bool) $trim_user;
            }
            if (isset($include_entities)) {
                $params['include_entities'] = (bool) $include_entities;
            }               
            $http_method = 'post';
            $path[] = $method;
        break;
        
        case 'retweets':
            $path[] = $method;
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
            if (isset($count)) {
                if (empty($count) || !is_numeric($count) || $count > 100) {
                    $invalid[] = 'count';
                } else {
                    $params['count'] = $count;
                }
            }         
        break;
        
        case 'retweeted_by':
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }        
            $path[] = $method;
            if (isset($count)) {
                if (empty($count) || !is_numeric($count) || $count > 100) {
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
        break;
        
        case 'retweeted_by_ids':
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }        
            $path[] = $method;
            $path[] = 'ids';
            if (isset($count)) {
                if (empty($count) || !is_numeric($count) || $count > 100) {
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
        break;
                            
        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'status', $method);
            return $response;
        break;
    }
    
    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'status', $method);
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