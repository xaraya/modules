<?php

/**
 * File: $Id$
 *
 * Get a blog posting
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include("modules/bloggerapi/xarincludes/common.php");

/**
 * Get a posting
 * 
 * Takes an xmlrpc enveloped message according to blogger api and
 * uses it to return a posting from Xaraya articles.
 *
 * @param  xmlrpcmsg   xml-rpc message with the parameters defined in blogger API
 * @return xmlrpcresp  Returns an xmlrpc response message, which contains the 
 *                     article on success or errormessage on failure
 * @see    xmlrpc_userapi_call(), xmlrpcresp, xmlrpcmsg
 */
function bloggerapi_userapi_getpost($msg) {
    xarLogMessage("blogger api: getPost");

	// get the params, we skip appkey for now..
	$sn1=$msg->getParam(1);  $postid   = $sn1->scalarval();
	$sn2=$msg->getParam(2);  $username   = $sn2->scalarval();
	$sn3=$msg->getParam(3);  $password   = $sn3->scalarval();
	
	if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) or wrong password while getting post",$username);
    } else {
        // FIXME: test for exceptions
        $article = xarModAPIFunc('articles','user','get',array('aid'=>$postid));
        if (!$article) {
            $err = xarML("Failed to retrieve article (#(1)",$postid);
        }
    }
    
	if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
	}	else {
		// convert date to iso date code
		$t = iso8601_encode($article['pubdate']);
        
		// create a struct for the response
        $data['userid']=$article['authorid'];
        $data['dateCreated']=$t;
				// FIXME: xmlrpc only requires <, > and & to be prepped, what do we do?
        $data['content']=xarVarPrepForDisplay($article['summary']);
        $data['postid']=$article['aid'];
        $output = _bloggerapi_createresponse('getpost',$data);
	}
    return $output;
	
}
?>