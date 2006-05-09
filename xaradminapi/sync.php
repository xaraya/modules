<?php

/**
 * Re-synchronise the totals and last posts of forums
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author mikespub
*/ 

/**
 * Re-synchronise forums and topics
 *
 * @param $args['fid'] int forum id (optional)
 * @param $args['withtopics'] bool update topics too (optional, default false)
 * @returns void
*/

function xarbb_adminapi_sync($args)
{
    // Security Check
    if (!xarSecurityCheck('EditxarBB', 1, 'Forum')) return;

    // Get parameters
    extract($args);

    if (!empty($fid)) {
        $forums = array();
        $forums[0] = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
        if (empty($forums[0])) return;
    } else {
        $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
    }

    $modid = xarModGetIDFromName('xarbb');
    $anonuid = xarConfigGetVar('Site.User.AnonymousUID');

    foreach ($forums as $forum) {
        $fid = $forum['fid'];

        // Get the settings for this forum
        //$settings = xarModGetVar('xarbb', 'settings.' . $fid);
        //if (isset($settings)){
        //    $settings = unserialize($settings);
        //}
        $settings = $forum['settings'];

        // get the number of topics
        $forum['ftopics'] = xarModAPIFunc('xarbb', 'user', 'counttopics', array('fid' => $fid));

        // get the number of posts
        $stats = xarModAPIFunc('comments', 'user', 'getmodules',
            array(
                'modid' => $modid,
                'itemtype' => $fid,
                'status' => 'active'
            )
        );

        if (isset($stats[$modid][$fid]['comments'])) {
            $forum['fposts'] = $forum['ftopics'] + $stats[$modid][$fid]['comments'];
        } else {
            $forum['fposts'] = $forum['ftopics'];
        }

        if (!empty($withtopics)) {
            $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics', array('fid' => $fid));
            $count = xarModAPIFunc('comments', 'user', 'getitems',
                array(
                    'modid' => $modid,
                    'itemtype' => $fid,
                    'status'   => 'active'
                )
            );

            foreach ($topics as $topic) {
                $tid = $topic['tid'];
                $param = array('tid' => $topic['tid'], 'time' => $topic['ttime']);

                if (!empty($count[$tid])) {
                    $param['treplies'] = $count[$tid];

                    // get the last comment
                    $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
                        array(
                            'modid'    => $modid,
                            'itemtype' => $fid,
                            'objectid' => $tid,
                            'startnum' => $count[$tid],
                            'numitems' => 1
                        )
                    );

                    $totalcomments = count($comments);
                    $isanon=$comments[$totalcomments-1]['xar_postanon'];

                    if ($isanon==1) {
                        $param['treplier'] = $anonuid;
                    } else {
                        $param['treplier'] = $comments[$totalcomments-1]['xar_uid'];
                    }

                    $param['time'] = $comments[$totalcomments-1]['xar_datetime'];
                } else {
                    $param['treplies'] = 0;
                    $param['treplier'] = 0;
                }
                $param['nohooks'] = true;

                if (!xarModAPIFunc('xarbb', 'user', 'updatetopic', $param)) return;
            }
        }

        // get the last topic
        if (!empty($forum['ftopics'])) {
            $list = xarModAPIFunc('xarbb', 'user', 'getalltopics',
                array(
                    'fid' => $fid,
                    'startnum' => 1, // already sorted by xar_ttime DESC
                    'numitems' => 1
                )
            );

            if (!empty($list)) {
                $last = $list[0];
                $forum['tid'] = $last['tid'];
                $forum['ttitle'] = $last['ttitle'];
                $forum['treplies'] = $last['treplies'];

                if (!empty($last['treplies'])) {
                    $forum['fposter'] = $last['treplier'];
                } else {
                    $forum['fposter'] = $last['tposter'];
                }

                $forum['fpostid'] = $last['ttime'];
            } else {
                $forum['tid'] = 0;
                $forum['ttitle'] = '';
                $forum['treplies'] = 0;
                $forum['fposter'] = 0;
                $forum['fpostid'] = time();
            }
        } else {
            $forum['tid'] = 0;
            $forum['ttitle'] = '';
            $forum['treplies'] = 0;
            $forum['fposter'] = 0;
            $forum['fpostid'] = time();
        }

        // update the forum view
        if (!xarModAPIFunc('xarbb', 'user', 'updateforumview', $forum)) return;
    }

    return true;
}

?>