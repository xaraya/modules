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
    $sn3=$msg->getParam(3);  $struct   = $sn3->getval();
    $sn4=$msg->getParam(4);  $publish  = $sn4->scalarval();
    
    // Before we do anything, see if we should
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) or password while editing post",$username);
        return xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }
    
    extract($struct);
    // Process further, first get the current article
    // Should we check?
    $article = xarModAPIFunc('articles','user','get',array('aid' => $postid, 'withcids' => true));
   
    // Title field
    if(!isset($title)) $title = $article['title'];
    // Main content
    if(!isset($description)) $description = $article['summary'];
    // Publication date
    if(!isset($dateCreated)) {
        $dateCreated = $article['pubdate'];
    } else {
        $dateCreated = iso8601_decode($struct['dateCreated']);
    } 
    
    // See if we got MT stuff
    $usingMT = isset($mt_allow_comments);
    // Extended entry
    if(!isset($mt_text_more)) {
        $body = $article['body'];
    } else {
        $body = $mt_text_more;
    }
    
    // categories are optional
    $pubType= xarModGetVar('bloggerapi','bloggerpubtype');
    $itemCats = $article['cids'];
    $cids = array();
    if($usingMT) {
        $cids = $itemCats; // MT does this separately
    } elseif(isset($categories)) {
        // We got some through the request
        foreach($categories as $index => $category) {
            $cids[] = xarModAPIFunc('categories','user','name2cid',array('name' => $category->scalarval()));
        }
        // Now we have all but the base cat
        $rootCats = array();
        $rootCats = xarModGetVar('articles','mastercids.'.$pubType);
        if(!empty($rootCats)) $rootCats = explode(';',$rootCats); 
        $blogCat = array_intersect($rootCats, $itemCats);
        $cids[] = $blogCat[0];
    }

    $status = 0; // Submitted
    if ($publish) {
        $status = xarModGetVar('bloggerapi','publishstatus'); 
    } else {
        $status = xarModGetVar('bloggerapi','draftstatus');
    }

    if (!xarModAPIFunc('articles','admin','update',array('aid'      => $article['aid'], 
                                                         'title'    => $title,
                                                         'summary'  => $description, 
                                                         'body'     => $body,
                                                         'ptid'     => $pubType, 
                                                         'cids'     => $cids, 
                                                         'status'   => $status))) {
        $err = "Failed to update post: $postid";
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