<?php

/**
 * File: $Id$
 *
 * List XML-RPC methods
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * List methods - show the known methods for the XML-RPC server
 *
 * This function is part of the 'informal' introspection API
 * which is not very well standardized.
 *
 * @param object $server XML-RPC server instance
 * @param string $msg    not used (but required)
 * @retun XML-RPC formatted message
 */
function xmlrpcsystemapi_userapi_listmethods($args) 
{
    extract($args); 
    extract($msg);
    // listmethods has no parameters, so $msg can be ignored
    $dmap=$server->dmap;
    $elements = array();
    
    // Construct an array of strings for the xmlrpcserver
    for(reset($dmap); list($key, $val)=each($dmap); ) {
        $elements[]=array('string',$key);
    }
    $params = array(array('array',$elements));
    /* 
     * The following call is an example to use the generic protocol template in the
     * xmlrpc server to construct a response. It uses a generic template which, based
     * on the contents of the $params is able to construct any XML-RPC response. 
     * The xmlrpcsystemapi_userapi_methodsignature API function contains an example of
     * using a second method to construct a response, using a template supplied by the API
     * module itself. Both methods lead to the same result, but choice is always good ;-)
     */
    $out = xarModAPIFunc('xmlrpcserver','user','createresponse',array('params'  => $params));

    return $out;
}
?>