<?php

/**
 * File: $Id$
 *
 * Provide help about a method
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcsystemapi_userapi_methodhelp($args) 
{
    extract($args);
    extract($msg);
    $methName=$msg->getParam(0);
    $methName=$methName->scalarval();
    $dmap = $server->dmap;
    if (ereg("^system\.", $methName)) {
        $sysCall=1;
    } else {
        $sysCall=0;
    }
    // Create a string parameter for the xmlrpcserver
    $responsedata = array();
    if (isset($dmap[$methName])) {
        if ($dmap[$methName]["docstring"]) {
            $responsedata[] = array('string', $dmap[$methName]['docstring']);
        } 
        // Another example of method 1: using xmlrpc server generic protocol template
        $out = xarModAPIFunc('xmlrpcserver','user','createresponse', array('params'  => $responsedata));
    } else {
        $err = xarML("The method #(1) is not known at this XML-RPC server",$methName);
        $out = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }
    return $out;
}
?>
