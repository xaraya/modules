<?php
/**
* File: $Id: s.xaruserapi.php 1.37 03/09/06 14:00:35+01:00 miko@power.dudleynet $
*
* Webservices user api
*
* @package modules
* @copyright (C) 2002 by the Xaraya Development Team.
* @link http://www.xaraya.com
* 
* @subpackage webservices
* @author Gregor J. Rothfuss
* @author Marcel van der Boom
* @author Michel Dalle
*/
 
/**
* Call any Xaraya API through SOAP
* 
* Passes the API request to the indicated Xaraya API, and gets back
* the result.
* @param $params['username'] a user authorized to call the API
* @param $params['password'] the password of that user
* @param $params['module'] the name of the module to call
* @param $params['type'] the type of the function, usually 'user' or 'admin'
* @param $params['func'] the name of the function to call
* @param $params['args'] the arguments for the function to call
* @returns resultarray
*/
function wsModAPIFunc(&$request_data)
{
    extract($request_data);

/*
$data = print_r($request_data, true);
$fd = fopen('modules/soapserver/logs/log.txt', 'a');
fwrite($fd, $data . "\n");
fclose($fd);
*/

    // If the soap client sent an extra layer in the array, then strip it out.
    if (isset($wsModApiFuncRequest) && is_array($wsModApiFuncRequest)) { 
        extract($wsModApiFuncRequest);
    }
    if (isset($wsModApiFuncSimpleRequest) && is_array($wsModApiFuncSimpleRequest)) { 
        extract($wsModApiFuncSimpleRequest);
    }
    

    if (empty($module)) {
        return new soap_fault('Client', 'Xaraya', 'Must supply a module name', ''); 
    }
    
    if (empty($type)) {
        return new soap_fault('Client', 'Xaraya', 'Must supply a type', ''); 
    }
    
    if (empty($func)) {
        return new soap_fault('Client', 'Xaraya', 'Must supply a function', ''); 
    }
    
    if (empty($username)) {
        return new soap_fault('Client', 'Xaraya', 'Must supply a user name', ''); 
    }
    
    if (empty($password)) {
        return new soap_fault('Client', 'Xaraya', 'Must supply a password', ''); 
    }

    if(!isset($args) || !is_array($args)) { 
        $args = array();
    }
    
    // Try to login
    if (!xarUserLogin($username,$password)) {
        return new soap_fault('Server', 'Xaraya',
            "Invalid username or password for ($username) to access API methods"
        ); 
    }

    // TODO: at this point, decide whether the user is actually allowed to cal
    // up this API.

    // Call the API
    $result = xarModAPIFunc($module, $type, $func, $args);
    
    if (empty($result)) {
        return new soap_fault('Server', 'Xaraya',
            xarErrorRender('text') . " for module '$module' type '$type' func '$func' with args " . join('-', $args), ''
        ); 
    } else {
        // Determine how to encode the return value.
        if (is_string($result)) {
            $return_type = 'string';
        } elseif (is_array($result)) {
            // If all keys are numeric then it's an array, otherwise treat it as a struct
            if (array_reduce(array_keys($result), create_function('$v,$w','return $v && is_numeric($w);'), true)) {
                $return_type = 'array';
            } else {
                $return_type = 'struct';
            }
        } elseif (is_bool($result)) {
            $return_type = 'boolean';
        } elseif (is_object($result)) {
            $return_type = 'struct';
        } else {
            // Default
            $return_type = 'string';
        }

        $out = new soapval('output', 'struct', $result);
        return $out->serialize();
    }

}

function wsModApiSimpleFunc(&$request_data) 
{ 
    return wsModAPIFunc($request_data);
}

function webservices_userapi_getmenulinks()
{
} 

?>
