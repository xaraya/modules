<?php

/**
* $Id$
 *
 * Implementation of the metaWeblog.getRecentPosts method
 *
 * The signatures of blogger.RecentPosts and metaWeblog.recentPosts are
 * the same, let the blogger api handle this
 *
 */

function metaweblogapi_userapi_getrecentposts($args)
{
    xarLogMessage("MetaWeblog api: getRecentPosts");
    extract($args);
    
    $sn1=$msg->getParam(0);  $blogid        = $sn1->scalarval();
    $sn2=$msg->getParam(1);  $username      = $sn2->scalarval();
    $sn3=$msg->getParam(2);  $password      = $sn3->scalarval();
    $sn4=$msg->getParam(3);  $numberOfPosts = $sn4->scalarval();
    
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
                                  array('startnum' => 1, 'ptid' => null, 'numitems' => $numberOfPosts, 'cids' => $cids));
        
        // No posts found does NOT constitute an error, but can be helpfull in debugging
        //if (count($articles)==0) {
        //    $cat = xarModAPIFunc('categories','user','cid2name',array('cid'=>$blogid));
        //    $err = xarML("No posts found in category (#(1))",$cat);
        //}
    }
    
    
    if (!empty($err)) {
        $output = xarModAPIFunc('xmlrpcserver','user','faultresponse',array('errorstring' => $err));
    } else {
        // otherwise, we create the right response
        $article_list=array(); $i = 0;
        $data=array();
        foreach ($articles as $article) {
            $article_list[$i]['title']       = $article['title'];
            $article_list[$i]['authorid']    = $article['authorid'];
            $article_list[$i]['dateCreated'] = iso8601_encode($article['pubdate']);
            $article_list[$i]['content']     = xarVarPrepForDisplay($article['summary']);
            $article_list[$i]['body']        = xarVarPrepForDisplay($article['body']);
            $article_list[$i]['postid']      = $article['aid'];
            $article_list[$i]['link']        = xarModUrl('articles','user','display',array('aid' => $article['aid']));
            $catnames = array();
            
            if(!empty($article['cids'])) {
                foreach($article['cids'] as $catid) {
                    if($catid == $blogid) continue;
                    $catname = xarModAPIFunc('categories','user','cid2name',array('cid' => $catid));
                    // the cat api func does a raw url encode, why o why is that in an api method?
                    $catnames[]['name'] = rawurldecode($catname);
                }
            }
            $article_list[$i]['categories'] = $catnames;
            $i++;
        }
                
        $data['articlelist'] = $article_list;
        
        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'metaweblogapi',
                                      'command' => 'getrecentposts',
                                      'params'  => $data)
                                );
    } 
    return $output;
}
?>