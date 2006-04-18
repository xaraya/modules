<?php

/** 
 * View a list of topics in a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @author Jo dalle Nogare
*/

function xarbb_user_viewforum()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'int:1', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('read', 'isset', $read, NULL, XARVAR_DONT_SET)) return;

    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    if (empty($data)) return;

    $now = time();

    if ($data['fstatus'] == 1) {
        // FIXME: this same message keeps cropping up over and over - could it be centralised?
        $msg = xarML('Forum -- #(1) -- has been locked by administrator', $data['fname']);
        xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_FORUM', new SystemException($msg));
        return;
    }

    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;
    xarTplSetPageTitle($data['fname']);

    // Grab the last visit timestamps.
    $lastvisitthisforum = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'f_' . $fid));
    $lastvisitallforums = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'lastvisit'));

    $lastvisitcompared = max($lastvisitthisforum, $lastvisitallforums);

    // And now we kill all of this work and just move on.
    xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'f_' . $fid, 'value' => $now));

    // Settings and display
    $data['items'] = array();
    $settings = unserialize(xarModGetVar('xarbb', 'settings.' . $fid));
    $data['showcats'] = $settings['showcats'];
    $data['xbbname'] = xarModGetVar('themes', 'SiteName');

    // Login
    $data['return_url'] = xarModURL('xarbb', 'user', 'viewforum', array('fid' => $fid));
    $data['submitlabel'] = xarML('Submit');

    $topicsperpage = $settings['topicsperpage'];

    // TODO, should be a forum setting
    $hotTopic = $settings['hottopic'];

    // CHECKME: retrieve from session variable, module user variable or URL param
    // depending on how customisable/cacheable we want to make this?
    if (!empty($settings['topicsortby'])) {
        $topicsortby = $settings['topicsortby'];
    } else {
        $topicsortby = 'time';
    }

    if (!empty($settings['topicsortorder'])) {
        $topicsortorder = $settings['topicsortorder'];
    } else {
        $topicsortorder = 'DESC';
    }

    // Fetch the topics for this page.
    // TODO: allow some form of user override on topics per page (within limits).
    $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics',
        array(
            'fid' => $fid,
            'sortby' => $topicsortby,
            'order' => $topicsortorder,
            'startnum' => $startnum,
            'numitems' => $settings['topicsperpage']
        )
    );
    $totaltopics = count($topics);

    $topiclist = array();
    $isuser = array();
    for ($i = 0; $i < $totaltopics; $i++) {
        $topiclist[] = $topics[$i]['tid'];
        $isuser[$topics[$i]['tposter']] = 1;
        $isuser[$topics[$i]['treplier']] = 1;
    }

    // FIXME: check whether hitcounts is installed, or even hooked, before calling its API.
    $hits = xarModAPIFunc('hitcount', 'user', 'getitems',
        array(
            'modname' => 'xarbb',
            'itemtype' => $fid,
            'itemids' => $topiclist
        )
    );
    $userlist = array_keys($isuser);
    $users = array();
    if (count($userlist) > 0) {
        $users = xarModAPIFunc('roles', 'user', 'getall', array('uidlist' => $userlist));
    }
 
    // Fetch the topic tracking array for this forum.
    // TODO: move this to a separate API, as we need to do this deserialisation thing a lot.
    if (!isset($read)) {
        // Normal handling
        $topic_tracking = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'topics_' . $fid));
        if (empty($topic_tracking)) {
            $topic_tracking = array();
        } else {
            $topic_tracking = unserialize($topic_tracking);
        }
    } else {
        // Special handling: the user has selected the 'mark all topics as read' option,
        // so we can discard the current value of the topic tracking array.
        $topic_tracking = array();
    }

    for ($i = 0; $i < $totaltopics; $i++) {
        // Assuming topics are zero-indexed.
        $topic = $topics[$i];
        $tid = $topic['tid'];

        // Update the topic tracking array, if required.
        if (!isset($topic_tracking[$tid])) {
            // Not in the array - add this topic if it contains posts
            // later than our last forum visit time.
            if ($lastvisitthisforum < $topic['ttime']) {
                $topic_tracking[$tid] = 0;
            }
        } else {
            // We do have latest visit time information for this topic.
            // If it has been updated since we last looked, then reset the time,
            // making this an 'unread' topic.
            if ($topic_tracking[$tid] > 0 && $topic_tracking[$tid] < $topic['ttime']) {
                $topic_tracking[$tid] = 0;
            }
        }

        // If the last visit time is zero, then this topic contains unread material.
        // If the last visit time is not zero, or is not set, then it is not a new topic.
        if (isset($topic_tracking[$tid]) && $topic_tracking[$tid] == 0) {
            $new_topic = true;
        } else {
            $new_topic = false;
        }

        list($topics[$i]['ttitle'], $topics[$i]['tpost']) = xarModCallHooks(
            'item', 'transform', $fid,
            array($topic['ttitle'], $topic['tpost']),
            'xarbb', $fid
        );

        // CHECKME: what does this bit do?
        if (isset($lastvisitsession)) {
            $data['lastvisitdate'] = $lastvisitcompared;
        } else {
            $data['lastvisitdate'] = time() - 60*60*24;
        }

        $topics[$i]['tpost'] = xarVarPrepHTMLDisplay($topic['tpost']);
        $topics[$i]['comments'] = xarVarPrepHTMLDisplay($topic['treplies']);

        switch(strtolower($topic['tstatus'])) {
            // Just a regular old topic
            case '0':
            default:
                if (!$new_topic) {
                    // More comments than our hottopic setting, therefore should be hot, but not new.
                    if ($topics[$i]['comments'] > $hotTopic) {
                        $topics[$i]['timeimage'] = 1;
                    // Else should be a regular old boring topic
                    } else {
                        $topics[$i]['timeimage'] = 2;
                    }
                } else {
                    // OOF, look at this topic, hot and new.
                    if ($topics[$i]['comments'] > $hotTopic) {
                        $topics[$i]['timeimage'] = 3;
                    // Else should be a regular old boring topic that has a new post
                    } else {
                        $topics[$i]['timeimage'] = 9;
                    }
                }
                break;
            // Announcement topic
            case '1':
                if (!$new_topic) {
                    $topics[$i]['timeimage'] = 4;
                } else {
                    $topics[$i]['timeimage'] = 5;
                }

                break;
            // Sticky topic
            case '2':
                if (!$new_topic) {
                    $topics[$i]['timeimage'] = 6;
                } else {
                    $topics[$i]['timeimage'] = 7;
                }
                break;
            // Locked
            case '3':
                $topics[$i]['timeimage'] = 8;
                break;
        }

        if (!empty($hits[$topic['tid']])) {
            $topics[$i]['hitcount'] = $hits[$topic['tid']];
        } else {
            $topics[$i]['hitcount'] = 0;
        }

        // CHECKME: is this relevant without the hitcount module?
        if (!$topics[$i]['hitcount']) {
            $topics[$i]['hitcount'] = '0';
        } elseif ($topics[$i]['hitcount'] == 1) {
            $topics[$i]['hitcount'] .= ' ';
        } else {
            $topics[$i]['hitcount'] .= ' ';
        }

        if (isset($users[$topic['tposter']])) {
            $topics[$i]['name'] = $users[$topic['tposter']]['name'];
        } else {
            $topics[$i]['name'] = '-';
        }

        // And we need to know who did the last reply

        if ($topics[$i]['comments'] == 0) {
            $topics[$i]['authorid'] = $topic['tposter'];
        } else {
            // TODO FIX THIS FROM COMMENTS
            $topics[$i]['authorid'] = $topic['treplier'];
        }

        if (isset($users[$topics[$i]['authorid']])) {
            $topics[$i]['replyname'] = $users[$topics[$i]['authorid']]['name'];
        } else {
            $topics[$i]['replyname'] = '-';
        }

        $topics[$i]['topicpager'] = xarTplGetPager(
            1, $topic['treplies'],
            xarModURL('xarbb', 'user', 'viewtopic', array('startnum' => '%%', 'tid' => $topic['tid'])),
            $topicsperpage, array(), 'multipage'
        );
    }
    xarbb_user_viewforum_sort_topics($topics);
    $data['items'] = $topics;

    // Store the topic tracking array for this forum.
    // TODO: sort and truncate it, limiting it to N elements.
    // TODO: should this be configurable, i.e. the number of topics to keep
    // track of, for each forum?
    $max_topic_tracking = 1;
    if (count($topic_tracking) > $max_topic_tracking) {
        // We need to remove the oldest topics from the array.
        // These will be the lowest non-zero element values.
        asort($topic_tracking);
        // TODO: remove a slice, which will be in the middle of the array
        // after the zero-values. If we run out of non-zero values
        // then start on the zero values, older (smaller) topic IDs first.
        $slice_size = count($topic_tracking) - $max_topic_tracking;
        //var_dump($topic_tracking);
    }
    // TODO: provide user with ability to reset this array, to mark all topics as read.
    xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'topics_' . $fid, 'value' => serialize($topic_tracking)));

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager(
        $startnum, $data['ftopics'],
        xarModURL('xarbb', 'user', 'viewforum', array('startnum' => '%%', 'fid' => $fid)),
        $settings['topicsperpage']
    );
    $categories = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $data['catid']));
    $data['catname'] = $categories['name'];

    // Forum Jump
    $data['forums'] = xarModAPIFunc('xarbb', 'user', 'getallforums');
    
    return $data;
}

/**
 *  Function to help sort the topics array by order of importance
 *  @params $topics array topics to be sorted passed in by reference
 *  @return null
 */
function xarbb_user_viewforum_sort_topics(&$topics)
{
    $normal = array();
    $sticky = array();
    $announcements = array();
    
    for($i=0, $max = count($topics); $i<$max; $i++) {
        switch($topics[$i]['tstatus']) {
            case '1':
                $announcements[] =& $topics[$i];
                break;
            case '2':
                $sticky[] =& $topics[$i];
                break;
            case '3':
                $normal[] =& $topics[$i];
                break;
            case '0':
            default:
                $normal[] =& $topics[$i];
                break;
        }
    }

    // merge the arrays and form the new topics array
    $topics = array_merge($announcements, $sticky, $normal);

    // get rid of these since we no longer need them in memory
    unset($announcements, $sticky, $normal);
}

?>