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
function xmlrpcsystemapi_userapi_methodsignature($args) {
    extract($args);

	$methName=$msg->getParam(0);
	$methName=$methName->scalarval();
    $dmap=$server->dmap;
	if (ereg("^system\.", $methName)) {
        $sysCall=1;
	} else {
		$sysCall=0;
	}
	//	print "<!-- ${methName} -->\n";
    $data = array();  $data['signatures'] = array();
	if (isset($dmap[$methName])) {
		if ($dmap[$methName]["signature"]) {
			$thesigs=$dmap[$methName]["signature"];
            // Function can have multiple signatures.
			for($i=0, $nrSigs=sizeof($thesigs); $i<$nrSigs; $i++) {
				$inSig=$thesigs[$i];
                // Reserve room for the params
                $data['signatures'][$i] = array();
				for($j=0, $nrParams=sizeof($inSig); $j<$nrParams; $j++) {
                    $data['signatures'][$i][] = $inSig[$j];
                }
			}
		} else {
            // Method exists, but signature is undefined, set 
            // both returntype and params to undef
            $data['signatures'][0][] = 'undef';
            $data['signatures'][0][] = 'undef';
		}
	} else {
        // Method is not in the dispatch map.
        $err = xarML("The method #(1) is not know at this XML-RPC server",$methName);
        $out = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
	}
    // Let xmlrpcserver construct the response
    $out = xarModAPIFunc('xmlrpcserver','user','createresponse',
                         array('module'  => 'xmlrpcsystemapi',
                               'command' => 'methodsignature',
                               'params'  => $data)
                         );
    return $out;
}
?>