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

    $tplData = array();
    $tplData['errno'] = XMLRPC_USER_ERR + $errno;
    $tplData['errorstring'] = $errorstring;
    
    // Disable inserting template comments if set
    $themecomments=xarModGetVar('themes','ShowTemplates');
    if($themecomments != 0) xarModSetVar('themes','ShowTemplates',0);
    
    $response = xarTplFile('modules/xmlrpcserver/xartemplates/xmlrpc-faultresponse.xd',$tplData);
    if($themecomments !=0) xarModSetVar('themes','ShowTemplates',$themecomments);

    return $response;
}
?>
