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
    $sn2=$msg->getParam(2);  $password  = $sn2->scalarval();
    $sn4=$msg->getParam(4);  $publish   = $sn4->scalarval();

    // Get the members from the struct which represents the content
    $sn3=$msg->getParam(3);
    $struct = $sn3->getval();
    
    $title = $struct['title'];
    $content = $struct['description'];
    $dateCreated = iso8601_decode($struct['dateCreated']);
    
    // categories are optional
    $categories = array();
    if(array_key_exists('categories', $struct)) {
        foreach($struct['categories'] as $index => $category) {
            $categories[] = $category->scalarval();
        }
    }
    
    // We now have gathered all our stuff, use this to post to xaraya through
    // the API
    if (!xarUserLogin($username,$password)) {
        $err = xarML("Invalid user (#(1)) or wrong password while creating new post",$username);
    } else {
        // Deal with the title
        if (empty($title)){
            $title = xarML("Post from #(1) on: #(2)",$username,date("Y-m-d"));
        }
        
        // Deal with the summary
        $summary = $content;
        
        // Deal with the categories
        $cids[] = $blogid; 
        if(!empty($categories)) {
            // Match the names we got from the client to ids, we only have to
            // consider the subcats of blogid
            foreach($categories as $index => $name) {
                $cids[] = xarModAPIFunc('categories','user','name2cid',array('name' => $name));
            }
        }
       
        $bodytype = ' ';  $bodytext = $content; $language = ' ';
        
        if ($publish) {
            $status ='publishstatus'; 
        } else {
            $status = 'draftstatus';
        }
        $status = xarModGetVar('bloggerapi',$status);
        if(empty($status)) $status = 0; // Submitted
                                        // FIXME: Test for exceptions
        $pubType= xarModGetVar('bloggerapi','bloggerpubtype');
        // FIXME: This shouldn't be necessary, but articles makes this into 01011970 
        $pubDate = $dateCreated;
        $postid = xarModAPIFunc('articles','admin','create',array('ptid'=>$pubType,
                                                                  'title'=>$title,'summary'=>$summary,
                                                                  'cids' => $cids,'bodytype'=>$bodytype, 'bodytext'=>$bodytext,
                                                                  'language'=>$language,
                                                                  'status' => $status,
                                                                  'pubdate' => $pubDate));
        xarLogMessage("Created article $postid with status $status ($publish) ");
        if (!$postid) {
            xarExceptionFree();
            $err = xarML("Failed to create new post #(1) (permission problem?)",$postid);
        }
    }
    
    if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    }    else {
        $data['postid'] = array('string', $postid);
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('params'  => $data));
    }
    return $output;
}

?>