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

function xmlrpcsystemapi_userapi_methodhelp($server, $msg) {
	global $xmlrpcerr, $xmlrpcstr;

	$methName=$msg->getParam(0);
	$methName=$methName->scalarval();
    $dmap=$server->dmap;
	if (ereg("^system\.", $methName)) {
		$sysCall=1;
	} else {
		$sysCall=0;
	}
	//	print "<!-- ${methName} -->\n";
	if (isset($dmap[$methName])) {
		if ($dmap[$methName]["docstring"]) {
			$r=new xmlrpcresp(new xmlrpcval($dmap[$methName]["docstring"]),
												"string");
		} else {
			$r=new xmlrpcresp(new xmlrpcval("", "string"));
		}
	} else {
			$r=new xmlrpcresp(0,
						  $xmlrpcerr["introspect_unknown"],
						  $xmlrpcstr["introspect_unknown"]);
	}
	return $r;
}
?>