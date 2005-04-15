<?php

/**
 * File: $Id$
 *
 * Initialization of XML-RPC server
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcserver
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Initialise the installed XML-RPC server APIs
 * 
 * Carries out a number of initialisation tasks to get various
 * XML-RPC libraries up and running.
 * @param none
 * @returns void
 */
function xmlrpcserver_userapi_initxmlrpcserver()
{
    xarLogMessage("xmlrpcserver: Initializing xmlrpc server");
    // include XML-RPC libraries
    include_once 'modules/xmlrpcserver/xarincludes/xmlrpc.inc';
    include_once 'modules/xmlrpcserver/xarincludes/xmlrpcs.inc';

    $functions=array();
    
    // Loop over the potentially available api's
    $apis = array('xmlrpcsystemapi', 'xmlrpcvalidatorapi', 'bloggerapi', 'metaweblogapi', 'moveabletype');
    foreach($apis as $index => $api) {
        if (xarModIsAvailable($api)) {
            xarLogMessage("xmlrpcserver: registering XML-RPC methods for $api");
            $_xmlrpc_methods = xarModAPIFunc($api, 'user', 'getdmap');
            $functions = array_merge($functions, $_xmlrpc_methods);
        } elseif($api == 'xmlrpcsystemapi') {
            // System API should always be available
            xarLogMessage("xmlrpcserver: XML-RPC system API not available, this is required");
            return false;
        }
    }
    
    //create server instance
    $server = new xmlrpc_server($functions);
    return $server;
}
?>
