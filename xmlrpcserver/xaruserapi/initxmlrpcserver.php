<?php

/**
 * File: $Id$
 *
 * Initialization of XML-RPC server
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
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
    
    // System API should always be available
    if (xarModIsAvailable('xmlrpcsystemapi')) {
        xarLogMessage("xmlrpcserver: registering XML-RPC system methods");
        $_xmlrpc_methods = xarModAPIFunc('xmlrpcsystemapi','user','getdmap');
        $functions = array_merge($functions, $_xmlrpc_methods);
    } else {
        xarLogMessage("xmlrpcserver: XML-RPC system API not available, this is required");
        return false;
    }

    // Blogger API
    if (xarModIsAvailable('bloggerapi')) {
        xarLogMessage("xmlrpcserver: registering XML-RPC Blogger methods");
        $_xmlrpc_methods = xarModAPIFunc('bloggerapi','user','getdmap');
        $functions = array_merge($functions, $_xmlrpc_methods);
    }
    
    // Validator1 API
    if (xarModIsAvailable('xmlrpcvalidatorapi')) {
        xarLogMessage('xmlrpcserver: registering XML-RPC validator methods');
        $_xmlrpc_methods = xarModAPIFunc('xmlrpcvalidatorapi','user','getdmap');
        $functions = array_merge($functions, $_xmlrpc_methods);
    }

    // MetaWebLog API
    //include_once 'modules/xmlrpcserver/api/metaweblog.php';
    // merge meta weblog functions
    //$functions = array_merge($functions, $_xmlrpc_metaweblog_dmap);
    
    //create server instance
    $server = new xmlrpc_server($functions);
    return $server;
}
?>
