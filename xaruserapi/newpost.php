<?php

/**
 *
 * Implementation of the metaWeblog.newPost method
 *
 *
 */

function metaweblogapi_userapi_newpost($args)
{
    extract($args);
    xarLogMessage("MetaWeblog api api: newPost");
    setlocale(LC_TIME, xarConfigGetVar('locale'));
    // Extract the parameters from the xmlrpc msg
    $sn0=$msg->getParam(0);  $blogid   = $sn0->scalarval();
    $sn1=$msg->getParam(1);  $username = $sn1->scalarval();
    $sn2=$msg->getParam(2);  $password = $sn2->scalarval();
    $sn3=$msg->getParam(3);  $struct   = $sn3->getval();
    $sn4=$msg->getParam(4);  $publish  = $sn4->scalarval();
    
    // TODO: add support for more members
    extract($struct); // Get all members of the struct
    
    // Title field, we map this to articles['title']
    if(!isset($title)) $title = xarML("Post from #(1) on: #(2)",$username,date("Y-m-d"));
    // Main entry, we map this to articles['summary']
    if(!isset($description)) $description ='';
    // dateCreated, we map this to article['pubdate']
    if(!isset($dateCreated)) {
        $dateCreated = time();
    } else {   
        $dateCreated = iso8601_decode($dateCreated);
    }
    // publish, we map this to article['status']
    if ($publish) {
        $status ='publishstatus'; 
    } else {
        $status = 'draftstatus';
    }
    // Retrieve our mapping
    $status = xarModGetVar('bloggerapi',$status);
    if(empty($status)) $status = 0; // Submitted   
    
    // categories are optional
    $cids[] = $blogid;
    if(isset($categories)) {
        foreach($categories as $index => $category) {
            $cids[] = xarModAPIFunc('categories','user','name2cid',array('name' => $category->scalarval()));
        }
    }
    // See if we got the MT extras
    if(isset($mt_text_more)) {
        $body = $mt_text_more;
    } else {
        $body ='';
    }
    $keywords ='';
    if(isset($mt_keywords)) {
        $keywords = $mt_keywords;
    }
       
    // We now have gathered all our stuff, use this to post to xaraya through the API
    if (empty($password) || !xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) or wrong password while creating new post",$username);
    } else {
        $pubType= xarModGetVar('bloggerapi','bloggerpubtype');
        $postid = xarModAPIFunc('articles','admin','create',array('ptid'      => $pubType,
                                                                  'title'     =>  $title,
                                                                  'summary'   => $description,
                                                                  'cids'      => $cids, 
                                                                  'body'      => $body,
                                                                  'status'    => $status,
                                                                  'pubdate'   => $dateCreated,
                                                                  'keywords'  => $keywords));
        xarLogMessage("Created article $postid with status $status ($publish) in (".join(',',$cids).")");
        if (!$postid) {
            xarErrorFree();
            $err = xarML("Failed to create new post #(1) (permission problem?)",$postid);
        }
    }
    
    if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    } else {
        $data['postid'] = array('string', $postid);
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',array('params'  => $data));
    }
    return $output;
}

?>