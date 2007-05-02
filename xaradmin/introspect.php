<?php

/**
 * File: $Id$
 *
 * GUI for introspection API
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcsystemapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcsystemapi_admin_introspect()
{
    xarVarFetch('listmethods','int',$listmethods,0);
    xarVarFetch('methodhelp','int',$methodhelp,0);
    xarVarFetch('payloads1','int',$payloads1,0);
    xarVarFetch('payloads2','int',$payloads2,0);
    xarVarFetch('method','str::',$method,'system.methodHelp');

    $data = array();

    // Instantiate a client
    include 'modules/xmlrpcserver/xarincludes/xmlrpc.inc';
    $host = xarServerGetHost();
    $path = xarServerGetBaseURI() . '/ws.php?type=xmlrpc';
    $client = new xmlrpc_client($path,$host);

    // List a combo with registered methods?
    if($listmethods) {
        $data['listmethods'] = 1;

        // We need to catch the debug output
        if($payloads1) {
            ob_start();
            $client->setDebug(1);
        }
        $request = new xmlrpcmsg('system.listMethods');
        $response = $client->send($request);
        if($payloads1) {
            $data['debugoutput'] = ob_get_contents();
            ob_end_clean();
        }

        $methodobjects = $response->xv->me['array'];
        $methods = array();
        if(!empty($methodobjects)) {
            foreach($methodobjects as $methodobject) {
                $methods[] = $methodobject->me['string'];
            }
        }
        $data['methods'] = $methods;
    }

    // List help for a specific method?
    if($methodhelp) {
        // We need to buffer debug output
        if($payloads2) {
            ob_start();
            $client->setDebug(1);
        }

        // Get the helptext for the method
        $request = new xmlrpcmsg('system.methodHelp',array(new xmlrpcval($method)));
        $response = $client->send($request);

        $methodHelp = $response->xv->me['string'];
        $data['methodhelp'] = xarVarPrepForDisplay($methodHelp);

        // Get the signature
        $request = new xmlrpcmsg('system.methodSignature',array(new xmlrpcval($method)));
        $response = $client->send($request);
        if($payloads2) {
            $data['debugoutput2'] = ob_get_contents();
            ob_end_clean();
        }

        $methodSignatures = array();
        $methodSignatures = $response->xv->me['array'];
        $data['methodtoshow'] = $method;

        // Gather the signatures
        $sigs = array();
        foreach($methodSignatures as $methodSignature) {
            $sig = $methodSignature->me['array'];
            // First element is the return type, rest are arguments
            $sigs['returntype'] = $sig[0]->me['string'];
            $sigparams = array();
            for($i=1; $i<sizeof($sig);$i++) {
                $sigparams[] = $sig[$i]->me['string'];
            }
            $sigs['params'] = $sigparams;
            $data['sigs'][] = $sigs;
        }
    }


    return $data;
}
?>
