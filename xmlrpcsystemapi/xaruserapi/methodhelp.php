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
   	$methName=$msg->getParam(0);
	$methName=$methName->scalarval();
    $dmap = $server->dmap;
	if (ereg("^system\.", $methName)) {
		$sysCall=1;
	} else {
		$sysCall=0;
	}
	//	print "<!-- ${methName} -->\n";
    $data = array();
    $data['methodhelp'] = '';
	if (isset($dmap[$methName])) {
		if ($dmap[$methName]["docstring"]) {
            $data['methodhelp'] = $dmap[$methName]['docstring'];
		} 
        $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                             array('module'  => 'xmlrpcsystemapi',
                                   'command' => 'methodhelp',
                                   'params'  => $data)
                             );
	} else {
        $err = xarML("The method #(1) is not know at this XML-RPC server",$methName);
        $out = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
	}
	return $out;
}
?>