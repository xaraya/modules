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
 */
function xmlrpcsystemapi_userapi_listmethods($server, $msg) {
    // listmethods has no parameters
	global $xmlrpcerr, $xmlrpcstr;
	$v=new xmlrpcval();
	$dmap=$server->dmap;
	$outAr=array();
	for(reset($dmap); list($key, $val)=each($dmap); ) {
		$outAr[]=new xmlrpcval($key, "string");
	}
	$v->addArray($outAr);
	return new xmlrpcresp($v);
}
?>