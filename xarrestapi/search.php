<?php
function twitter_restapi_search($args)
{
    extract($args);
    
    $path[] = 'search';    
    $http_method = 'get';
    $invalid = array();
    
    if (isset($callback)) {
        if (empty($callback) || !is_string($callback)) {
            $invalid[] = 'callback';
        } else {
            $params['callback'] = $callback;
        }
    }

    if (isset($lang)) {
        if (empty($lang) || !is_string($lang)) {
            $invalid[] = 'lang';
        } else {
            $params['lang'] = $lang;
        }
    }

    if (isset($locale)) {
        if (empty($locale) || !is_string($locale)) {
            $invalid[] = 'locale';
        } else {
            $params['locale'] = $locale;
        }
    }
    
    if (isset($max_id)) {
        if (empty($max_id) || !is_numeric($max_id)) {
            $invalid[] = 'max_id';
        } else {
            $params['max_id'] = $max_id;
        }
    }
    
    if (isset($q)) {
        if (empty($q) || !is_string($q)) {
            $invalid[] = 'q';
        } else {
            $params['q'] = $q;
        }
    }
    
    if (isset($rpp)) {
        if (empty($rpp) || !is_numeric($rpp) || $rpp > 100) {
            $invalid[] = 'rpp';
        } else {
            $params['rpp'] = $rpp;
        }
    }
    
    if (isset($page)) {
        if (empty($page) || !is_numeric($page)) {
            $invalid[] = 'page';
        } else {
            $params['page'] = $page;
        }
    }
    
    if (isset($since)) {
        if (!empty($since) && is_numeric($since)) 
            $since = date("Y-m-d");
        if (empty($since) || !is_string($since)) {
            $invalid[] = 'since';
        } else {
            $params['since'] = $since;
        }
    }
    
    if (isset($since_id)) {
        if (empty($since_id) || !is_numeric($since_id)) {
            $invalid[] = 'since_id';
        } else {
            $params['since_id'] = $since_id;
        }
    }
    
    if (isset($geocode)) {
        if (empty($geocode) || !is_string($geocode)) {
            $invalid[] = 'geocode';
        } else {
            $params['geocode'] = $geocode;
        }
    }
    
    if (isset($show_user)) {
        if ((bool) $show_user) {
            $params['show_user'] = 'true';
        }
    }
    
    if (isset($until)) {
        if (!empty($until) && is_numeric($until)) 
            $until = date("Y-m-d");
        if (empty($until) || !is_string($until)) {
            $invalid[] = 'until';
        } else {
            $params['until'] = $until;
        }        
    }

    $result_types = array('mixed', 'recent', 'popular');    
    if (isset($result_type)) {
        if (!is_string($result_type) || !in_array($result_type, $result_types)) {
            $invalid[] = 'result_type';
        } else {
            $params['result_type'] = $result_type;
        }
    }
    
    if (empty($params)) {
        $invalid[] = 'params';
    }

    if (!empty($invalid)) {
        $response['error'] = xarML('Invalid #(1) for Twitter API #(2) method', join(', ', $invalid), 'search');
        return $response;
    }

    if (empty($consumer_key) || empty($consumer_secret)) {
        $consumer_key = xarModGetVar('twitter', 'consumer_key');
        $consumer_secret = xarModGetVar('twitter', 'consumer_secret');    
    }

    $response = xarModAPIFunc('twitter', 'rest', '_process', 
        array(
            'path' => $path,
            'params' => $params,
            'http_method' => $http_method,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'cached' => isset($cached) ? $cached : null,
            'expires' => isset($expires) ? $expires : null,
        ));
    
    return $response;
}
?>