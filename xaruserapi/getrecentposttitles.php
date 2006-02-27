<?php
/**
 * Return the dispatch mapping for the moveabletype api
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @author Marcel van der Boom <marcel@xaraya.com>
 */
function moveabletype_userapi_getrecentposttitles($args)
{
  xarLogMessage("Moveabletype api: getRecentPostTitles", XARLOG_LEVEL_WARNING);
  extract($args);

  // get the params
  $sn1=$msg->getParam(0); $blogid        = $sn1->scalarval();
  $sn2=$msg->getParam(1); $username      = $sn2->scalarval();
  $sn3=$msg->getParam(2); $password      = $sn3->scalarval();
  $sn4=$msg->getParam(3); $numberOfPosts = $sn4->scalarval();

  //Try to login
  $err='';
  if(empty($password) || !xarUserLogin($username, $password)) {
    $err = xarML("Invalid username or password for (#(1)) while getting recent post titles", $username);
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
          // return needs: date, userid, postid, title
          $article_list[$i]['dateCreated'] = iso8601_encode($article['pubdate']);
          $article_list[$i]['authorid']    = $article['authorid'];
          $article_list[$i]['postid']      = $article['aid'];
          $article_list[$i]['title']       = $article['title'];
          $i++;
        }

        $data['articlelist'] = $article_list;

        $output = xarModAPIFunc('xmlrpcserver','user','createresponse',
                                array('module'  => 'moveabletype',
                                      'command' => 'getrecentposttitles',
                                      'params'  => $data)
                                );
    }
    return $output;

}

?>