<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * modify webservices configuration
 */
function soapserver_user_calltest()
{
/*
    // Get parameters
    // I guess these are required, still added the 'not required' just for being sure...
    if (!xarVarFetch('googlekey', 'isset', $key,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('phrase',    'isset', $phrase, NULL, XARVAR_DONT_SET)) return;

    // Security Check
//    if(!xarSecurityCheck('ReadWS')) return;

    // The API function is called.
    $answer = xarModAPIFunc('soapserver',
                            'user',
                            'callsoap',
                            array('type' => 'soap',
                                  'methodname' => 'doSpellingSuggestion',
                                  'params' => array('key' => $key, 'phrase' => $phrase),
                                  'endpoint' => array('site' => 'http://api.google.com', 'path' => '/search/beta2'),
                                  'namespace' => 'urn:GoogleSearch'));
*/

    // The API function is called.
    $answer = xarModAPIFunc('soapserver',
                            'user',
                            'callsoap',
                            array('type' => 'soap',
                                  'methodname' => 'wsModAPISimpleFunc',
                                  'params' => array('username' => 'username', 'password' => 'password', 'module' => 'articles', 'func' => 'getAll', 'type' => 'user'),
                                  'endpoint' => array('site' => 'http://epicsaga.com', 'path' => '/ws.php?type=soap'),
                                  'namespace' => 'urn:XarayaSoap'));


    // Success
    $data['answer'] = $answer;

    // Return
    return $data;

}

?>
