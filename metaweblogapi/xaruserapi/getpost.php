<?php

/**
 * Implementation of the metaWeblog.getPost method
 *
 */
function metaweblogapi_userapi_getpost($args) 
{
    extract($args);
    xarLogMessage("metaWeblog api api: getPost");
    
    // get the params, we skip appkey for now..
    $sn1=$msg->getParam(0);  $postid   = $sn1->scalarval();
    $sn2=$msg->getParam(1);  $username   = $sn2->scalarval();
    $sn3=$msg->getParam(2);  $password   = $sn3->scalarval();
    
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
    }    else {
        // Get the categories for this article.
        
        $data['title'] = $article['title'];
        $data['userid']=$article['authorid'];
        $data['dateCreated']=iso8601_encode($article['pubdate']);
        $data['categories'] = array();
        $data['content']=xarVarPrepForDisplay($article['summary'];);
        $data['postid']=$article['aid'];
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'metaweblogapi',
                                      'command' => 'getpost',
                                      'params'  => $data));
    }
    return $output;
    
}

?>