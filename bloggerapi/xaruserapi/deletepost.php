<?php

/**
 * File: $Id$
 *
 * Delete a blog posting
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Delete a posting
 * 
 * Takes an xmlrpc enveloped message according to blogger api and
 * uses it to delete a posting from Xaraya articles.
 *
 * @param  xmlrpcmsg   xml-rpc message with the parameters defined in blogger API
 * @return xmlrpcresp  Returns an xmlrpc response message, which contains a true value on success or error on failure
 * @see    xmlrpc_userapi_call(), xmlrpcresp, xmlrpcmsg
 */
function bloggerapi_userapi_deletepost($msg) 
{
    xarLogMessage("blogger api: deletePost");
    
	// get the params, we skip appkey and publish for now..
	$sn1=$msg->getParam(1); $postid   = $sn1->scalarval();
	$sn2=$msg->getParam(2); $username = $sn2->scalarval();
	$sn3=$msg->getParam(3); $password = $sn3->scalarval();
	
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) while trying to delete post",$username);
    } else {
        if (!xarModAPIFunc('articles','admin','delete',array('aid'=>$postid))) {
            // Prevent exception to propagate
            xarExceptionFree();
            $err = xarML("Failed to delete post #(1)",$postid);
        }
    }
    
	if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
	}	else {
		// otherwise, we create the right response (boolean)
        $output = xarModAPIFunc('xmlrpcserver','user','successresponse');
    }
    return $output;
}
?>