<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * MethodSignatur
 */
function xmlrpcsystemapi_userapi_methodsignature($server, $msg) {
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
		if ($dmap[$methName]["signature"]) {
			$sigs=array();
			$thesigs=$dmap[$methName]["signature"];
			for($i=0; $i<sizeof($thesigs); $i++) {
				$cursig=array();
				$inSig=$thesigs[$i];
				for($j=0; $j<sizeof($inSig); $j++) {
					$cursig[]=new xmlrpcval($inSig[$j], "string");
				}
				$sigs[]=new xmlrpcval($cursig, "array");
			}
			$r=new xmlrpcresp(new xmlrpcval($sigs, "array"));
		} else {
			$r=new xmlrpcresp(new xmlrpcval("undef", "string"));
		}
	} else {
			$r=new xmlrpcresp(0,
						  $xmlrpcerr["introspect_unknown"],
						  $xmlrpcstr["introspect_unknown"]);
	}
	return $r;
}
?>