<?php

/**
* File: $Id$
 *
 * Return an array with xmlrpc datatypes
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpc server
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function xmlrpcserver_userapi_getdatatypes()
{
    return array('xmlrpcI4'       => 'i4',
                 'xmlrpcInt'      => 'int',
                 'xmlrpcBoolean'  => 'boolean',
                 'xmlrpcDouble'   => 'double',
                 'xmlrpcString'   => 'string',
                 'xmlrpcDateTime' => 'dateTime.iso8601',
                 'xmlrpcBase64'   => 'base64',
                 'xmlrpcArray'    => 'array',
                 'xmlrpcStruct'   => 'struct');
}

?>