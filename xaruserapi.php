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
* @author Jason Judge
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
function wsModAPIFunc($module, $type = '', $func = '', $username = '', $password = '', $args = array())
{
    // If all the parameters come in as a single struct, then extract them.
    if (is_array($module)) extract($module);

/*
$data = print_r($request_data, true);
$fd = fopen('modules/soapserver/logs/log.txt', 'a');
fwrite($fd, $data . "\n");
fclose($fd);
*/

    // If the soap client sent an extra layer in the array, then strip it out.
    // For legacy reasons, the arguments may be passed over SOAP as straight arguments,
    // or wrapped in a single polymorphic struct - we need to be able to cater for both.
    if (isset($wsModApiFuncRequest) && is_array($wsModApiFuncRequest)) { 
        extract($wsModApiFuncRequest);
    }
    if (isset($wsModApiFuncSimpleRequest) && is_array($wsModApiFuncSimpleRequest)) { 
        extract($wsModApiFuncSimpleRequest);
    }
    

    if (empty($module) || !xarVarValidate('pre:vtoken:str:1', $module)) {
        $error = new nusoap_fault('Client', 'Xaraya', 'Missing module name', ''); 
        return $error;
    }
    
    if (empty($type) || !xarVarValidate('pre:vtoken:str:1', $type)) {
        $error = new nusoap_fault('Client', 'Xaraya', 'Missing API type', ''); 
        return $error;
    }
    
    if (empty($func) || !xarVarValidate('pre:vtoken:str:1', $func)) {
        $error = new nusoap_fault('Client', 'Xaraya', 'Missing API function', ''); 
        return $error;
    }
    
    if (empty($username)) {
        $error = new nusoap_fault('Client', 'Xaraya', 'Missing user name', ''); 
        return $error;
    }
    
    if (empty($password)) {
        $error = new nusoap_fault('Client', 'Xaraya', 'Missing password', ''); 
        return $error;
    }

    if (!isset($args) || !is_array($args)) { 
        $args = array();
    }
    
    // Try to login
    if (!xarUserLogin($username,$password)) {
        $error = new nusoap_fault('Server', 'Xaraya',
            "Invalid username or password for ($username) to access API methods"
        ); 
        return $error;
    }

    // TODO: at this point, decide whether the user is actually allowed to call this API.
    // A mapping of role groups against module/type/functions, with wildcard support, may be a way to go.

    // Get a list of matching rows from the 'allowed.txt' list of APIs
    $allowed_file_path = 'modules/soapserver/config/allowed.txt';
    if (!is_file($allowed_file_path) || !is_readable($allowed_file_path)) {
        $allowed_file_path = 'modules/soapserver/config/allowed.dist.txt';
    }

    $allowed_file = realpath($allowed_file_path);
    $lines = @file($allowed_file);
    if (!empty($lines)) {
        $preg_pattern = "/^($module|\*):($type|\*):($func|\*)(\s|$)/";
        $api_matches = preg_grep($preg_pattern, $lines);

        // If we have at least one match, then the API is allowed
        // TODO: we then need to check the role groups for each line matched.
        if (empty($api_matches)) {
            $error = new nusoap_fault('Server', 'Xaraya', "Access to API '$module:$type:$func' is not permitted");
            return $error;
        }
    }

    // Call the API
    $result = xarModAPIFunc($module, $type, $func, $args);
    
    // CHECKME: the result is largely irrelevent. It is the exception that tells us
    // something went wrong. Remove the 'isset' check?
    if (!isset($result) || xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        $error = new nusoap_fault('Server', 'Xaraya',
            xarErrorRender('text') . " for module '$module' type '$type' func '$func' with args " . join('-', $args), ''
        ); 
        return $error;
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
            // Default (nil?)
            $return_type = 'string';
        }

        $out = new soapval('output', $return_type, $result);
        // CHECK: not sure why this needs to be serialised here as the soap server
        // ought to be able to work out how to serialize the soapval object.
        return $out->serialize();
    }
}

function wsModApiSimpleFunc($module, $type = '', $func = '', $username = '', $password = '', $args = array()) 
{ 
    return wsModAPIFunc($module, $type, $func, $username, $password, $args);
}

function webservices_userapi_getmenulinks()
{
} 

?>
