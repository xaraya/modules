<?php

/**
 * File: $Id$
 *
 * Construct a XML-RPC faultresponse
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpserver
 * @link http://www.xmlrpc.com/spec
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

// User errors start at 800
define('XMLRPC_USER_ERR',800);

function xmlrpcserver_userapi_faultresponse($args) 
{
    $errno = 1; $errorstring =  xarML('No error');
    extract($args, EXTR_OVERWRITE);
    xarLogMessage("Issuing errorno $errno: $errorstring", XARLOG_LEVEL_WARNING);
    
    $data=array(); $members = array(); $params = array();
    $members[] = array('faultCode', 'int', XMLRPC_USER_ERR + $errno);
    $members[] = array('faultString', 'string', $errorstring);
    $params[]  = array('struct', $members);
    
    $data['params'] = $params;
    $data['fault']  = 1;
    
    //xarLogMessage(print_r($data,true),XARLOG_LEVEL_WARNING);
    $out = xarModApiFunc('xmlrpcserver','user','createresponse', $data);
    
    //xarLogMessage("Returning: $out", XARLOG_LEVEL_WARNING);
    return $out;
}
?>
