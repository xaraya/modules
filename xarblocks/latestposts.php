<?php

/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
*/
/**
 * Display latest xarbb topics and posts
 * @author jojodee
 */

function xarbb_latestpostsblock_init()
{
    return array(
        'nocache' => 0, // cache by default
        'pageshared' => 1, // share across pages
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
}

/**
 * get information on block
 */
function xarbb_latestpostsblock_info()
{
    // Values
    return array(
        'text_type' => 'latestposts',
        'module' => 'xarbb',
        'text_type_long' => xarML('Show Latest Topics and Posts'),
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */      
function xarbb_latestpostsblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ViewxarBB', 0)) return;
    if (empty($blockinfo['content'])) return '';

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (!isset($vars['addtopics'])) $vars['addtopics'] = 'on';
    if (!isset($vars['addposts'])) $vars['addposts'] = 'on';
    if (!isset($vars['latestpost'])) $vars['latestpost'] = 'off';
    if (empty($vars['howmany'])) $vars['howmany'] = 5;
    if (empty($vars['forumid'])) $vars['forumid'] = array(0 =>'all');
    if (!isset($vars['addauthor'])) $vars['addauthor'] = '2';
    if (!isset($vars['addcomment'])) $vars['addcomment'] = '2';
    if (!isset($vars['addobject'])) $vars['addobject'] = '1';
    if (!isset($vars['adddate'])) $vars['adddate'] = 'on';
    if (empty($vars['truncate'])) $vars['truncate'] = 18;
    if (!isset($vars['addlink'])) $vars['addlink'] = '2';
    if (!isset($vars['forumlink'])) $vars['forumlink'] = '2';
/*
    if (empty($vars['titleortext'])) {
        $vars['titleortext'] = 'on';
    }
*/

    $vars['order'] = 'DESC';

    //Get all the forums involved
    $forumset = array();

    if ($vars['forumid'][0] == 'all') {
        $forumdata=$vars['forumid'];

        //get all the available forums in the array - in $fid
        $forumset=xarModAPIFunc('xarbb', 'user', 'getallforums');
    } else {
       //we already have the array in forumid
        $forumdata=$vars['forumid']; //in $forums[$i]

        foreach ($forumdata as $forum) {
            $forumset[]=xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $forum));
        }
    }

    $modid = xarModGetIDFromName('xarbb');
    $alltopics = array();
    $forumnames = array();
    //Get all latest topics for given forums
    foreach($forumset as $forum) {
        // TODO: retrieve topics from all (selected) forums at once?
        // A: yes, that can be done now
        $alltopics[]=xarModAPIFunc(
            'xarbb', 'user', 'getalltopics',
            array('fid'=>$forum['fid'], 'numitems'=>$vars['howmany'])
        );

        // keep track of the forum names
        $forumnames[$forum['fid']] = $forum['fname'];
    }

    $postlist=array();
    $topiclist=array();
    $usernames = array();
    //Get all topic posters for given topics
    foreach ($alltopics as $topics) {
        foreach ($topics as $topic) {
            $forumname = $forumnames[$topic['fid']];

            if (($vars['addtopics']=='on') ||($vars['latestpost'] == 'on')) {
                if (!isset($usernames[$topic['tposter']])) {
                    $posterdata=xarModAPIFunc('roles', 'user', 'get', array('uid' => $topic['tposter']));
                    if (empty($posterdata)) {
                        $usernames[$topic['tposter']] = '-';
                    } else {
                        $usernames[$topic['tposter']] = $posterdata['name'];
                    }
                }
                $username = $usernames[$topic['tposter']];
                if ($topic['tstatus'] == 5){
                    $topic['tid'] = $topic['tpost'];
                }
                //Put each topic in consistent format for post comparison
                $postlist[] = array(
                    'tid'       => $topic['tid'],
                    'fid'       => $topic['fid'],
                    'fname'     => $forumname,
                    'title'     => substr($topic['ttitle'], 0, $vars['truncate']),
                    'poster'    => $topic['tposter'],
                    'postername'=> $username,
                    'ptime'     => $topic['tftime'],
                    'ptext'     => substr($topic['tpost'], 0, $vars['truncate']),
                    'anon'      => 0,
                    'link'      => xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $topic['tid'])),
                    'flink'     => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $topic['fid']))
                );
                if (($topic['treplies'] == 0) && ($vars['latestpost'] == 'on') && ($vars['addtopics'] == 'on')){
                    $topiclist[]=array(
                        'tid'       => $topic['tid'],
                        'fid'       => $topic['fid'],
                        'fname'     => $forumname,
                        'title'     => substr($topic['ttitle'], 0, $vars['truncate']),
                        'poster'    => $topic['tposter'],
                        'postername'=> $username,
                        'ptime'     => $topic['tftime'],
                        'ptext'     => substr($topic['tpost'], 0, $vars['truncate']),
                        'anon'      => 0,
                        'link'      => xarModURL('xarbb', 'user', 'viewtopic',array('tid' => $topic['tid'])),
                        'flink'     => xarModURL('xarbb', 'user', 'viewforum',array('fid' => $topic['fid']))
                    );
                }
            }
        }
    }

    if (($vars['addposts'] == 'on') || ($vars['latestpost'] == 'on')) {
        if ($vars['latestpost'] =='on') {
            //let's set how many posts to get
            $getnumber = 1; // we only want latest post
        } else {
            // TODO: skip retrieving comments when we already have 'howmany' topics/comments
            //       and the topic last post is older ?
            $getnumber = $vars['howmany'];
        }

        foreach ($alltopics as $topics) {
            foreach ($topics as $topic) {
                //Get all the most recent replies for each topic
                $posts = xarModAPIFunc(
                    'xarbb', 'user', 'get_allposts',
                    array('objectid' => $topic['tid'], 'itemtype' => $topic['fid'], 'numitems' => $getnumber)
                );
                $forumname = $forumnames[$topic['fid']];
                //Put post data in suitable list
                if (count($posts) >0 ) {
                    foreach ($posts as $post) {
                        $postlist[]=array(
                            'tid'       => $topic['tid'],
                            'fid'       => $topic['fid'],
                            'fname'     => $forumname,
                            'title'     => substr($post['xar_title'], 0, $vars['truncate']),
                            'poster'    => $post['xar_uid'],
                            'postername'=> $post['xar_author'],
                            'ptime'     => $post['xar_datetime'],
                            'ptext'     => substr($post['xar_text'], 0, $vars['truncate']),
                            'anon'      => $post['xar_postanon'],
                            'link'      => xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $topic['tid'])),
                            'flink'     => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $topic['fid']))
                        );
                        if ($vars['latestpost'] == 'on') {
                            $topiclist[]=array(
                                'tid'       => $topic['tid'],
                                'fid'       => $topic['fid'],
                                'fname'     => $forumname,
                                'title'     => substr($post['xar_title'],0,$vars['truncate']),
                                'poster'    => $post['xar_uid'],
                                'postername'=> $post['xar_author'],
                                'ptime'     => $post['xar_datetime'],
                                'ptext'     => substr($post['xar_text'],0,$vars['truncate']),
                                'anon'      => $post['xar_postanon'],
                                'link'      => xarModURL('xarbb','user','viewtopic',array('tid'=>$topic['tid'])),
                                'flink'     => xarModURL('xarbb','user','viewforum',array('fid'=>$topic['fid']))
                            );
                        }
                    }
                }
            }
        }
    }

    // let's set the new list if we have latestpost only switched on
    // The topic list only contains topics with no replies and first reply posts
    if ($vars['latestpost'] == 'on') $postlist = $topiclist;

    //Sort all the postlist by time
    usort($postlist, 'xarbb_datesort');
    $numposts = count($postlist);

    if ($numposts > $vars['howmany']) {
        $requiredposts = $vars['howmany'];
    } else {
        $requiredposts = $numposts;
    }
    $blockposts = array();
    for ($i = 0; $i < $requiredposts; $i++) {
      $blockposts[] = $postlist[$i];
    }

    $blockinfo['content'] = array('items' => $blockposts, 'vars' => $vars);

    return $blockinfo;
}

function xarbb_datesort($a, $b) 
{
    return ($a['ptime'] < $b['ptime'] ? true : false);
}

?>