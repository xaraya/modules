<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2004 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Author: jojodee
// Purpose of file: Show latest xarBB posts
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function xarbb_latestpostsblock_init()
{
    return true;
}

/**
 * get information on block
 */
function xarbb_latestpostsblock_info()
{
    // Values
    return array('text_type' => 'latestposts',
                 'module' => 'xarbb',
                 'text_type_long' => 'Show Latest Topics and Posts',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */      
function xarbb_latestpostsblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadxarBB')) {
        return;
    }
    if (empty($blockinfo['content'])) {
        return '';
    }

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['addtopics'])) {
        $vars['addtopics'] = 'on';
    }
    if (empty($vars['addposts'])) {
        $vars['addposts'] = 'on';
    }
    if (empty($vars['howmany'])) {
        $vars['howmany'] = 5;
    }
    if (empty($vars['forumid'])) {
        $vars['forumid'] = Array('All forums');
    }
    if (!isset($vars['addauthor'])) {   
        $vars['addauthor'] = '2';
    }
    if (!isset($vars['addcomment'])) {
        $vars['addcomment'] = '2';
    }
    if (!isset($vars['addobject'])) {
        $vars['addobject'] = '1';
    }
    if (empty($vars['adddate'])) { 
        $vars['adddate'] = 'on';
    }
    if (empty($vars['truncate'])) {
        $vars['truncate'] = 18;
    }
    if (!isset($vars['addlink'])) {
        $vars['addlink'] = '2';
    }
   if (!isset($vars['forumlink'])) {
        $vars['forumlink'] = '2';
    }
/*
    if (empty($vars['titleortext'])) {
        $vars['titleortext'] = 'on';
    }
*/

    $vars['order']='DESC';

    //Get all the forums involved
    $forumlist = array();

    if ($vars['forumid'][0] == 'all') {
        $forumdata=$vars['forumid'];
      //get all the available forums in the array - in $fid
      $forumset=xarModAPIFunc('xarbb','user','getallforums');

    } else {
       //we already have the array in forumid
        $forumdata=$vars['forumid']; //in $forums[$i]
        foreach ($forumdata as $forum) {
            $forumset[]=xarModAPIFunc('xarbb','user','getforum', array('fid'=>$forum));

        }
    }
    $modid=xarModGetIDFromName('xarbb');
    $postlist = array();
    //Get all latest topics for given forums
    foreach($forumset as $forum) {
        $alltopics[]=xarModAPIFunc('xarbb','user','getalltopics',array('fid'=>$forum['fid'],
                                                                       'numitems'=>$vars['howmany']));
    }
        //Get all latest posts for a given topic
        foreach ($alltopics as $topics) {
           foreach ($topics as $topic) {
              $forum=xarModAPIFunc('xarbb','user','getforum',array('fid'=>$topic['fid']));
              $fname=$forum['fname'];
               if ($vars['addtopics']=='on'){
                   $posterdata=xarModAPIFunc('roles',
                                         'user',
                                         'get',
                                          array('uid' => $topic['tposter']));
                   //Put each topic post in consistent format for post comparison
                   $postlist[]=array('tid'=> $topic['tid'],
                          'fid'       => $topic['fid'],
                          'fname'     => $fname,
                          'title'     => substr($topic['ttitle'],0,$vars['truncate']),
                          'poster'    => $topic['tposter'],
                          'postername'=> $posterdata['name'],
                          'ptime'     => $topic['ttime'],
                          'ptext'     => substr($topic['tpost'],0,$vars['truncate']),
                          'anon'      => 0,
                          'link'      => xarModURL('xarbb','user','viewtopic',array('tid'=>$topic['tid'])),
                          'flink'     => xarModURL('xarbb','user','viewforum',array('fid'=>$topic['fid']))
                          );
               }
               if ($vars['addposts']=='on'){
                    //Get all the post data for this topic
                    $posts[] = xarModAPIFunc('comments',
                              'user',
                              'get_multiple',
                              array('modid'       => $modid,
                                    'objectid'    => $topic['tid'],
                                    'numitems' => $vars['howmany']));
                    //Put post data in suitable list
                    foreach ($posts as $post) {
                        foreach ($post as $value=>$key) {
                            $posterdata=xarModAPIFunc('roles',
                                                  'user',
                                                  'get',
                                                  array('uid' => $key['xar_uid']));
                            if($topic['treplies']>0) {
                                 $postlist[]=array('tid'       => $topic['tid'],
                                          'fid'       => $topic['fid'],
                                          'fname'     => $fname,
                                          'title'     => substr($key['xar_title'],0,$vars['truncate']),
                                          'poster'    => $key['xar_uid'],
                                          'postername'=> $posterdata['name'],
                                          'ptime'     => $key['xar_datetime'],
                                          'ptext'     => substr($key['xar_text'],0,$vars['truncate']),
                                          'anon'      => $key['xar_postanon'],
                                          'link'      => xarModURL('xarbb','user','viewtopic',array('tid'=>$topic['tid'])),
                                          'flink'     => xarModURL('xarbb','user','viewforum',array('fid'=>$topic['fid']))
                                          );
                            }
                        }
                    }
                }
            }
        }
    //Sort all the postlist by time
    usort($postlist, 'xarbb_datesort');
    $numposts=count($postlist);
    if ($numposts>$vars['howmany']) {
        $requiredposts=$vars['howmany'];
    } else {
        $requiredposts=$numposts;
    }
    $blockposts=array();
    for ($i = 0; $i < $requiredposts; $i++) {
      $blockposts[]=$postlist[$i];
    }

    $blockinfo['content']=array('items' => $blockposts,
                                'vars'  => $vars);
    return $blockinfo;
}

function xarbb_datesort($a, $b) {

    return ($a['ptime'] < $b['ptime']);

}
?>
