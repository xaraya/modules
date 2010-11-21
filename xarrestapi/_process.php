<?php
sys::import('modules.twitter.class.twitterapi');
function twitter_restapi__process($args)
{
    extract($args);
    
    if (empty($consumer_key) || empty($consumer_secret)) {
        $consumer_key = xarModVars::get('twitter', 'consumer_key');
        $consumer_secret = xarModVars::get('twitter', 'consumer_secret');    
    }
    
    if (empty($access_token) || empty($access_token_secret)) {
        $access_token = null;
        $access_token_secret = null;
    }
    
    if (empty($http_method)) 
        $http_method = 'get';
    
    if (!empty($path) && is_array($path)) 
        $path = join('/', $path);    
    
    if (empty($path) || !is_string($path)) 
        $invalid[] = 'path';    
    
    if (empty($params))
        $params = array();
    
    $connection = new TwitterAPI($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    
    if (isset($cached))
        $connection->cached = $cached;
    if (isset($expires) && is_numeric($expires))
        $connection->expires = $expires;   
    
    $response = $connection->$http_method($path, $params);
    //if ($connection->http_code != 200)
      //  return false;    
    
    return $response;             
    
}
?>