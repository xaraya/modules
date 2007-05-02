<?php

/**
 * File: $Id$
 *
 * Success response handler for xmlrpc-server
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpc server
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcserver_userapi_successresponse($args) 
{
    extract($args);
    $data = array(); $params = array();
    $data['params'][] = array('boolean', 1);

    return xarModApiFunc('xmlrpcserver','user','createresponse', $data);
}
?>