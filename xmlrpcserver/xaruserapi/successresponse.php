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

function xmlrpcserver_userapi_successresponse() 
{
    $tplData = array();

    // Disable inserting template comments if set
    $themecomments=xarModGetVar('themes','ShowTemplates');
    if($themecomments != 0) xarModSetVar('themes','ShowTemplates',0);
    
    $response = xarTplFile('modules/xmlrpcserver/xartemplates/xmlrpc-successresponse.xd',array());
    if($themecomments !=0) xarModSetVar('themes','ShowTemplates',$themecomments);

    return $response;
}
?>