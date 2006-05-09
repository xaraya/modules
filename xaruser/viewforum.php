<?php

/**
 * View a list of topics in a forum
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
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
    if (!xarVarFetch('read', 'bool', $read, false, XARVAR_DONT_SET)) return;

    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    if (empty($data)) return;

    $now = time();

    if ($data['fstatus'] == 1) {
        // FIXME: this same message keeps cropping up over and over - could it be centralised?
        // FIXME: nicer handling of this error required.
        $msg = xarML('Forum -- #(1) -- has been locked by administrator', $data['fname']);
        xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_FORUM', new SystemException($msg));
        return;
    }

    // Security Check
    // CHECKME: the security check has already been done in the getforum API?
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum', $data['catid'] . ":$fid")) return;
    xarTplSetPageTitle($data['fname']);

    // Grab the last visit timestamps.
    $lastvisitthisforum = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'f_' . $fid));

    xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'f_' . $fid, 'value' => $now));

    // Settings and display
    $data['items'] = array();
    $settings = $data['settings'];
    $data['showcats'] = $settings['showcats'];
    $data['xbbname'] = xarModGetVar('themes', 'SiteName');

    $topicsperpage = $settings['topicsperpage'];
    $postsperpage = $settings['postsperpage'];

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
    // TODO: perhaps we can remove topics from this list after they are aged out, perhaps after
    // 24 hours, or some cofigurable value? This would help to keep the array size down.
    if (empty($read)) {
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

        // TODO: can be suppress transform of title? Block tags and anchors are the main problem.
        list($topics[$i]['ttitle'], $topics[$i]['tpost']) = xarModCallHooks(
            'item', 'transform', $fid,
            array($topic['ttitle'], $topic['tpost']),
            'xarbb', $fid
        );

        // CHECKME: what does this bit do? Why the 24 hours thing?
        if (isset($lastvisitsession)) {
            $data['lastvisitdate'] = $lastvisitcompared;
        } else {
            $data['lastvisitdate'] = time() - 60*60*24;
        }

        $topics[$i]['tpost'] = $topic['tpost'];
        $topics[$i]['comments'] = $topic['treplies'];
        $topics[$i]['icon_flags']['new'] = ($new_topic ? true : false);

        // Set a default image for old templates. (legacy support)
        $topics[$i]['timeimage'] = 2;

        $topics[$i]['hitcount'] = (!empty($hits[$topic['tid']]) ? $hits[$topic['tid']] : 0);

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
            $postsperpage, array(), 'multipage'
        );
    }
    xarbb_user_viewforum__sort_topics($topics);
    $data['items'] = $topics;

    // Store the topic tracking array for this forum.
    // Sort and truncate it, limiting it to N elements.
    // TODO: should this be configurable, i.e. the number of topics to keep
    // track of, for each forum?
    // Make the maximum large enough to track up to five pages of topics.
    $max_topic_tracking = (!empty($topicsperpage) ? $topicsperpage*5 : 100);
    if (count($topic_tracking) > $max_topic_tracking) {
        // We need to remove the oldest topics from the array.
        // These will be the lowest non-zero element values.
        // The oldest elements will be at the end after sorting.
        // Pre-sort by the topic IDs (the keys) first, in case all topics in
        // the array are marked 'unread'.
        krsort($topic_tracking);
        uasort($topic_tracking, 'xarbb_user_viewforum__cmp');

        // Calculate the size of the slice to remove, then pop them off the end of the array.
        $slice_size = count($topic_tracking) - $max_topic_tracking;
        for (; $slice_size > 0; $slice_size--) array_pop($topic_tracking);
    }
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

    // Forum Jump data
    $data['forums'] = xarModAPIFunc('xarbb', 'user', 'getallforums');

    return $data;
}

/**
 * Helper function for sorting the topic tracking array.
 */
function xarbb_user_viewforum__cmp($a, $b)
{
    if ($a == $b) return 0;
    return (($a == 0 || $a < $b) ? -1 : 1);
}

/**
 *  Function to help sort the topics array by order of importance
 *  @params $topics array topics to be sorted passed in by reference
 *  @return null
 */
function xarbb_user_viewforum__sort_topics(&$topics)
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