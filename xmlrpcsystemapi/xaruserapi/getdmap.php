<?php

/**
 * File: $Id$
 *
 * Return the dispatch map for the system api
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Register the dmap for the system api
 *
 */
function xmlrpcsystemapi_userapi_getdmap() 
{
    // Data types for xmlrpc
    $dataTypes = xarModAPIFunc('xmlrpcserver','user','getdatatypes');
    extract($dataTypes);
    
    $listMethods_sig=array(array($xmlrpcArray, $xmlrpcString), array($xmlrpcArray));
    $listMethods_doc='This method lists all the methods that the XML-RPC server knows how to dispatch';
    
    $methodSignature_sig=array(array($xmlrpcArray, $xmlrpcString));
    $methodSignature_doc='Returns an array of known signatures (an array of arrays) for the method name passed. If no signatures are known, returns a none-array (test for type != array to detect missing signature)';
    
    $methodHelp_sig=array(array($xmlrpcString, $xmlrpcString));
    $methodHelp_doc='Returns help text if defined for the method passed, otherwise returns an empty string';
    
    $dmap=array(
                "system.listMethods" =>
                array("function" => "xmlrpcsystemapi_userapi_listmethods",
                      "signature" => $listMethods_sig,
                      "docstring" => $listMethods_doc),
                "system.methodHelp" =>
                array("function" => "xmlrpcsystemapi_userapi_methodhelp",
                      "signature" => $methodHelp_sig,
                      "docstring" => $methodHelp_doc),
                "system.methodSignature" =>
                array("function" => "xmlrpcsystemapi_userapi_methodsignature",
                      "signature" => $methodSignature_sig,
                      "docstring" => $methodSignature_doc)
                );
    return $dmap;   
}
?>