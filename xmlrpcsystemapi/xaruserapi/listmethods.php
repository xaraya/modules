<?php

/**
 * File: $Id$
 *
 * List XML-RPC methods
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * List methods - show the known methods for the XML-RPC server
 *
 * This function is part of the 'informal' introspection API
 * which is not very well standardized.
 *
 * @param object $server XML-RPC server instance
 * @param string $msg    not used (but required)
 * @retun XML-RPC formatted message
 */
function xmlrpcsystemapi_userapi_listmethods($args) {
    extract($args);
    // listmethods has no parameters, so $msg can be ignored
	$dmap=$server->dmap;
	$data['methods'] = array();
	for(reset($dmap); list($key, $val)=each($dmap); ) $data['methods'][]=$key;
    $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                         array('module'  => 'xmlrpcsystemapi',
                               'command' => 'listmethods',
                               'params'  => $data)
                         );
	return $out;
}
?>