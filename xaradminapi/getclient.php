<?php


function xorba_adminapi_getclient($args)
{
    extract($args);
    
    sys::import('modules.xorba.xarclass.phpbeans.client.beanclient');

    $client = new PHP_Bean_Client($xorba_server,$xorba_port);
    $client->connect();

    // Authenticate
    $auth = $client->getObject('auth');
    $auth->identify($xorba_user,$xorba_pass);
    
    return $client;
}
?>