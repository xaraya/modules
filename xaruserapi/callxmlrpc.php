<?php

/**
 * File: $Id$
 *
 * Call a XML-RPC method
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Call a remote XML-RPC method
 * 
 * Opens a XML-RPC connection
 * with the specified parameters.
 * @returns resultrarray
 * @access private
 */
function xmlrpcserver_userapi__callXMLRPC($methodname, $params, $endpoint)
{
    
    // include XML-RPC libraries
    include_once 'modules/xmlrpcserver/lib/xmlrpc.inc';
    
    // build the XML-RPC call and execute it
    $f=new xmlrpcmsg($methodname,
                     array(new xmlrpcval($name), new xmlrpcval($url)));
    $c=new xmlrpc_client($endpoint['path'], $endpoint['site'], $endpoint['port']);
    $r=$c->send($f);
    if (!$r) { die('xmlrpcserver_userapi_call: send failed'); }
    $v=$r->value();
    if (!$r->faultCode()) {
        $result = array();
        for ($i = 0; $i <= $v; $i++)
            {
                $val = $v->structmem($i); $result[$i] = $val->scalarval();
            }
    } else {
        $result = 'Fault. Code: '.$r->faultCode().' Reason '.$r->faultString();
    }
    return $result;
    
}
?>
