<?php

/**
 * File: $Id$
 *
 * Edit a blog posting
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Edit a posting
 * 
 * Takes an xmlrpc enveloped message according to blogger api and
 * uses it to modify a posting from Xaraya articles.
 *
 * @param  xmlrpcmsg   xml-rpc message with the parameters defined in blogger API
 * @return xmlrpcresp  Returns an xmlrpc response message, which contains true 
 *                     on success or errormessage on failure
 * @see    xmlrpc_userapi_call(), xmlrpcresp, xmlrpcmsg
 */
function bloggerapi_userapi_editpost($msg) 
{ 
    xarLogMessage("blogger api: editPost");

    // get the params, we skip appkey
    $sn1=$msg->getParam(1);  $postid   = $sn1->scalarval();
	$sn2=$msg->getParam(2);  $username   = $sn2->scalarval();
	$sn3=$msg->getParam(3);  $password   = $sn3->scalarval();
	$sn4=$msg->getParam(4);  $content   = $sn4->scalarval();
    $sn5=$msg->getParam(5);  $publish   = $sn5->scalarval();
	xarLogVariable('publish',$publish);

    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) or password while editting post",$username);
 	} else {
        // FIXME: test for exceptions
        $article = xarModAPIFunc('articles','user','get',array('aid'=>$postid));
        $iids = array(); $iids[] = $postid;
        
        ereg("<title>(.*)</title>",$content, $title);
        $title =$title[1];
        $content = ereg_replace("<title>(.*)</title>","",$content);
        if (empty($title)) {
	        $title = $article['title'];
        }

        // FIXME: test for exceptions
        $cids = xarModAPIFunc('categories','user','getlinks',array('iids'=>$iids,'modid'=>xarModGetIDFromName('articles'),'reverse'=>0));
        if (!xarModAPIFunc('articles','admin','update',array('aid'=>$article['aid'], 'title'=>$title,
                                                            'summary'=>$content, 'cids' =>$cids,
                                                            'bodytype'=>'normal', 'bodytext'=>$article['body'],'language'=>' '))) {
               $err = "Failed to update post: $postid";
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