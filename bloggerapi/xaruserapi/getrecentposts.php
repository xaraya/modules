<?php

/**
 * File: $Id$
 *
 * Get recent blog postings
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Get recent postings
 * 
 * Takes an xmlrpc enveloped message according to blogger api and
 * uses it to return the n most recent postings from Xaraya articles.
 *
 * @param  xmlrpcmsg   xml-rpc message with the parameters defined in blogger API
 * @return xmlrpcresp  Returns an xmlrpc response message, which contains the 
 *                     articles on success or errormessage on failure
 * @see    xmlrpc_userapi_call(), xmlrpcresp, xmlrpcmsg
 */
function bloggerapi_userapi_getrecentposts($msg) {
    xarLogMessage("blogger api: getRecentPosts");
    // get the params, we skip appkey for now..
    $sn1=$msg->getParam(1);  $blogid   = $sn1->scalarval();
    $sn2=$msg->getParam(2);  $username   = $sn2->scalarval();
    $sn3=$msg->getParam(3);  $password   = $sn3->scalarval();
    $sn4=$msg->getParam(4);  $numberOfPosts   = $sn4->scalarval();
	
    // Try to login 
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid username or password for (#(1)) while getting recent posts",$username);
    } else {
        $uid = xarUserGetVar('uid');
        // When number of posts requested is zero, get all articles in requested category
        $cids = array();
        $cids[] = $blogid;
        if ($numberOfPosts == 0) {
            // Get all articles
            // FIXME: test for exeptions here!
            $numberOfPosts = xarModAPIFunc('articles','user','countitems',array('cids'=> $cids));
        }
        
        // Retrieve articles from selected category
        // FIXME: test for exceptions 
        $articles = xarModAPIFunc('articles','user','getall', 
            array('startnum' => 1, 'ptid' => null, 'numitems' => $numberOfPosts));
        
        if (count($articles)==0) {
					$cat = xarModAPIFunc('categories','user','getcat',array('return_itself'=>true,'cid'=>$blogid));
					$err = xarML("No posts found in category (#(1))",$cat[0]['name']);
        }
    }
    
	
	if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
	} else {
        // otherwise, we create the right response
        $articlelist=array(); $i = 0;
		$data=array();
        foreach ($articles as $article) {
            // FIXME: the title flagging needs to be configurable
            $content="<title>".$article['title']."</title>".$article['summary'];
            // convert date to iso date code
            $t = iso8601_encode($article['pubdate']);
            $article_list[$i]['authorid']=$article['authorid'];
            $article_list[$i]['dateCreated'] = $t;
            $article_list[$i]['content'] = xarVarPrepForDisplay($content);
            $article_list[$i]['postid'] = $article['aid'];

            $i++;
		}

        $data['articlelist'] = $article_list;

        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'bloggerapi',
                                      'command' => 'getrecentposts',
                                      'params'  => $data)
                                );
    } 
    return $output;
}
?>