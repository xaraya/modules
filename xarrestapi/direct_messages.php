<?php
function twitter_restapi_direct_messages($args)
{
    extract($args);

    $invalid = array();
    if (empty($method)) $method = null;
    /*
    if (empty($method) || !is_string($method))
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'direct messages');
        return $response;
    }
    */

    $path = array('direct_messages');
    $params = array();

    switch ($method) {

        case 'sent':
            $path[] = $method;
        default:
            if (isset($since_id)) {
                if (empty($since_id) || !is_numeric($since_id)) {
                    $invalid[] = 'since_id';
                } else {
                    $params['since_id'] = $since_id;
                }
            }
            if (isset($max_id)) {
                if (empty($max_id) || !is_numeric($max_id)) {
                    $invalid[] = 'max_id';
                } else {
                    $params['max_id'] = $max_id;
                }
            }
            if (isset($count)) {
                if (empty($count) || !is_numeric($count) || $count > 200) {
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

        case 'new':
            $path[] = $method;
            $http_method = 'post';
            if (!isset($user) || (!is_string($user) && !is_numeric($user))) {
                $invalid[] = 'user';
            } else {
                $params['user'] = $user;
            }
            if (empty($text) || !is_string($text)) {
                $invalid[] = 'text';
            } else {
                $params['text'] = $text;
            }
        break;

        case 'destroy':
            $path[] = $method;
            $http_method = 'delete';
            if (!isset($id) || !is_numeric($id)) {
                $invalid[] = 'id';
            } else {
                $path[] = $id;
            }
        break;
    }

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'direct messages', $method);
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