<?php

/**
 * File: 
 *
 * Implementation of the metaWeblog.editPost method
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage metaweblogapi
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

//metaWeblog.editPost (postid, username, password, struct, publish) returns true
function metaweblogapi_userapi_editpost($args) 
{ 
    extract($args);
    xarLogMessage("metaWeblog api: editPost");
    
    // get the params, we skip appkey
    $sn0=$msg->getParam(0);  $postid   = $sn0->scalarval();
    $sn1=$msg->getParam(1);  $username = $sn1->scalarval();
    $sn2=$msg->getParam(2);  $password = $sn2->scalarval();
    $sn4=$msg->getParam(4);  $publish  = $sn4->scalarval();
    
    // Get the members from the struct which represents the content
    // TODO: move this to an api function
    $sn3=$msg->getParam(3);
    $struct = $sn3->getval();
    
    $title = $struct['title'];
    $content = $struct['description'];
    if(array_key_exists('dateCreated', $struct)) {
        $dateCreated = iso8601_decode($struct['dateCreated']);
    } else {
        $dateCreated = time();
    }
    $usingMT=false;
    if(array_key_exists('mt_allow_comments', $struct)) {
        $usingMT =true;
    }
    
    // categories are optional
    $categories = array();
    if(array_key_exists('categories', $struct)) {
        foreach($struct['categories'] as $index => $category) {
            $categories[] = $category->scalarval();
        }
    }
 
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) or password while editing post",$username);
    } else {
        // FIXME: test for exceptions
        $article = xarModAPIFunc('articles','user','get',array('aid' => $postid, 'withcids' => true));
        $iids = array(); $iids[] = $postid;
        
        // Should we error out here maybe?
        if (empty($title)) {
            $title = $article['title'];
        }
        
        // FIXME: test for exceptions
        $pubType= xarModGetVar('bloggerapi','bloggerpubtype');
        $modId = 151;
        $cids = array();
        if($usingMT) {
            // MT has a separate method for setting the post categories, just copy them over here
            $cids = $article['cids'];
        } else {
            foreach($categories as $catname) {
                $cids[] = xarModAPIFunc('categories','user','name2cid',array('name' => $catname));
            }
        }
        if ($publish) {
            $status ='publishstatus'; 
        } else {
            $status = 'draftstatus';
        }
        $status = xarModGetVar('bloggerapi',$status);
        if(empty($status)) $status = 0; // Submitted
        if (!xarModAPIFunc('articles','admin','update',array('aid'=>$article['aid'], 'title'=>$title,
                                                             'summary'=>$content, 'ptid' => $pubType, 'cids' => $cids, 'status' => $status,
                                                             'bodytype'=>'normal', 'bodytext'=>$article['body'],'language'=>' '))) {
            $err = "Failed to update post: $postid";
        }
    }
    
    if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }    else {
        // otherwise, we create the right response (boolean)
        $output = xarModAPIFunc('xmlrpcserver','user','successresponse');
    }
    return $output;
}
    ?>