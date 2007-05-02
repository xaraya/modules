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
 * Call a remote Xmlrpcserver method
 * 
 * Opens an Xmlrpcserver connection
 * with the specified parameters.
 * @returns resultrarray
 */
function xmlrpcserver_userapi_call($args)
{
    
    if ($args['type'] == "xmlrpc") {
        return xmlrpcserver_userapi__callXMLRPC($args['methodname'], $args['params'], $args['endpoint']);
    }
   
}
?>
