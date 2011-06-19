<?php
function twitter_restapi_list($args)
{
    extract($args);

    $invalid = array();
    if (empty($method) || !is_string($method))
        $invalid[] = 'method';

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'list');
        return $response;
    }

    $path = array();
    $params = array();

    switch ($method) {

        case 'create':
            $http_method = 'post';
            $path[] = 'lists';
            if (!isset($name) || !is_string($name)) {
                $invalid[] = 'name';
            } else {
                $params[] = 'name';
            }
            if (isset($mode)) {
                $modes = array('public', 'private');
                if (empty($mode) || !is_string($mode) || !in_array($mode, $modes)) {
                    $invalid[] = 'mode';
                } else {
                    $params['mode'] = $mode;
                }
            }
            if (isset($description)) {
                if (empty($description) || !is_string($description)) {
                    $invalid[] = 'description';
                } else {
                    $params['description'] = $description;
                }
            }
        break;

        case 'update':
            $http_method = 'post';
            $path[] = 'lists';
            if (isset($name)) {
                if (empty($name) || !is_string($name)) {
                    $invalid[] = 'name';
                } else {
                    $params[] = 'name';
                }
            }
            if (isset($mode)) {
                $modes = array('public', 'private');
                if (empty($mode) || !is_string($mode) || !in_array($mode, $modes)) {
                    $invalid[] = 'mode';
                } else {
                    $params['mode'] = $mode;
                }
            }
            if (isset($description)) {
                if (empty($description) || !is_string($description)) {
                    $invalid[] = 'description';
                } else {
                    $params['description'] = $description;
                }
            }
            if (empty($params)) {
                $invalid[] = 'params';
            }
        break;

        case 'index':
            if (isset($user)) {
                if (!is_string($user) && !is_numeric($user)) {
                    $invalid[] = 'user';
                } else {
                    $path[] = $user;
                }
            }
            $path[] = 'lists';
            if (isset($cursor)) {
                if (empty($cursor) || !is_numeric($cursor)) {
                    $invalid[] = 'cursor';
                } else {
                    $params['cursor'] = $cursor;
                }
            }
        break;

        case 'show':
            if (isset($user)) {
                if (!is_string($user) && !is_numeric($user)) {
                    $invalid[] = 'user';
                } else {
                    $path[] = $user;
                }
            }
            $path[] = 'lists';
            if (empty($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
        break;

        case 'destroy':
            $http_method = 'delete';
            $path[] = 'lists';
            if (empty($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
        break;

        case 'statuses':
            if (isset($user)) {
                if (!is_string($user) && !is_numeric($user)) {
                    $invalid[] = 'user';
                } else {
                    $path[] = $user;
                }
            }
            $path[] = 'lists';
            if (empty($list_id) || (!is_numeric($list_id) && !is_string($list_id))) {
                $invalid[] = 'list_id';
            } else {
                $path[] = $list_id;
            }
            $path[] = $method;
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

        case 'memberships':
        case 'subscriptions':
            if (isset($user)) {
                if (!is_string($user) && !is_numeric($user)) {
                    $invalid[] = 'user';
                } else {
                    $path[] = $user;
                }
            }
            $path[] = 'lists';
            $path[] = $method;
            if (isset($cursor)) {
                if (empty($cursor) || !is_numeric($cursor)) {
                    $invalid[] = 'cursor';
                } else {
                    $params['cursor'] = $cursor;
                }
            }
        break;

        default:
            $response['error'] = xarML('Unknown Twitter API #(1) method "#(2)"', 'list', $method);
            return $response;
        break;
    }

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method #(3)', join(', ', $invalid), 'list', $method);
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