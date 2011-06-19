<?php
function twitter_restapi_geo($args)
{
    extract($args);

    $invalid = array();
    if (empty($method) || !is_string($method))
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'geo');
        return $response;
    }

    $path = array('geo');
    $params = array();

    switch ($method) {

        case 'nearby_places':
            if (isset($ip)) {
                if (empty($ip) || !is_string($ip)) {
                    $invalid[] = 'ip';
                } else {
                    $params['ip'] = $ip;
                }
            }
        case 'reverse_geocode':
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
            if (isset($accuracy)) {
                if (empty($accuracy) || !is_string($accuracy)) {
                    $invalid[] = 'accuracy';
                } else {
                    $params['accuracy'] = $accuracy;
                }
            }
            if (isset($granularity)) {
                if (empty($granularity) || !is_string($granularity)) {
                    $invalid[] = 'granularity';
                } else {
                    $params['granularity'] = $granularity;
                }
            }
            if (isset($max_results)) {
                if (empty($max_results) || !is_numeric($max_results)) {
                    $invalid[] = 'max_results';
                } else {
                    $params['max_results'] = $max_results;
                }
            }
        break;

        case 'id':
            $path[] = $method;
            if (empty($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
        break;

        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'geo', $method);
            return $response;
        break;
    }

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'geo', $method);
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