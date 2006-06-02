<?php

/**
 * Get all topics in a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * get all topics
 *
 * @param $args['fid'] forum id, or
 * @param $args['fids'] array of forum ids
 * @param $args['tid'] topic id, or
 * @param $args['tids'] array of topic ids
 * @param $args['uid'] user id (poster)
 * @param $args['from'] integer from timestamp (unix time)
 * @param $args['minreplies'] integer minimum number of replies
 * @param $args['maxreplies'] integer minimum number of replies
 * @param $args['ip'] string topics from a given IP address
 * @param $args['sortby'] string optional sort field (default 'time')
 * @param $args['order'] string optional sort order (default 'DESC' for time, replies etc.)
 * @param $args['cids'] array of category ids
 * @param $args['q'] string list of search terms (words)
 * @param $args['qrule'] string default rule
 * @param $args['qarea'] string csv list of areas to search (title, post)
 * @param $args['getcount'] boolean Requests that the function just return a count of topics, rather than the topics
 * @returns array
 * @return array of links, or false on failure
 * @todo allow this function to be used to return counts, as well as items, the difficulty being privilege checking
 */

function xarbb_userapi_getalltopics($args)
{
    extract($args);

    // Optional argument
    if (!isset($startnum)) {$startnum = 1;}
    // CHECKME: should numitems just be left NULL?
    if (!isset($numitems)) {$numitems = -1;} 

    if (empty($fids)) {$fids = array();}
    if (empty($tids)) {$tids = array();}

    // A single category or an array can be supplied.
    if (!empty($cid)) {$cids = array($cid);}
    if (empty($cids)) {$cids = array();}

    // We need to restrict the query by forums to which the
    // user has access. If no forum IDs have been passed in
    // then go grab them now.
    // TODO: getallforums() does not use the cids or fids - it would help if it did.
    if (empty($fid) && empty($fids)) {
        $all_forums = xarModAPIfunc('xarbb', 'user', 'getallforums',
            array('cids' => $cids, 'fids' => $fids)
        );
        foreach($all_forums as $forum) {
            $fids[] = $forum['fid'];
        }
    }
 
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];
    $xbbforumstable = $xartable['xbbforums'];

    if (!xarModAPILoad('categories', 'user')) return;

    // Get link
    $categoriesdef = xarModAPIFunc(
        'categories','user','leftjoin',
        array('cids' => $cids, 'modid' => xarModGetIDFromName('xarbb'))
    );
    if (empty($categoriesdef)) return;

    // CHECKME: this won't work for forums that are assigned to more (or less) than 1 category
    // Do we want to support that in the future? (Yes, but will need a major overhaul to do so)
    // Fix for duplicate listings of topics with topic itemtypes - select distinct - get bug #2335
    $bindvars = array();

    if (empty($getcount)) {
        $query = "SELECT xar_tid, $xbbtopicstable.xar_fid, xar_ttitle, xar_tpost, xar_tposter,"
            . " xar_ttime, xar_tftime, xar_treplies, xar_tstatus, xar_treplier, xar_toptions,"
            . " xar_fname, xar_fdesc, xar_ftopics, xar_fposts, xar_fposter, xar_fpostid,"
            . " xar_fstatus, xar_thostname, {$categoriesdef['cid']}";
    } else {
        $query = "SELECT COUNT(*)";
    }

    $query .= " FROM $xbbtopicstable "
        . " LEFT JOIN $xbbforumstable ON $xbbtopicstable.xar_fid = $xbbforumstable.xar_fid"
        . " LEFT JOIN {$categoriesdef['table']} ON {$categoriesdef['field']} = $xbbforumstable.xar_fid"
        . " {$categoriesdef['more']}"
        . " WHERE {$categoriesdef['where']}";

    // bug #2335 - some older upgrades of xarbb seem to need the following to prevent duplicates
    $query .= " AND {$categoriesdef['itemtype']} = 0";

    // Single forum ID
    if (isset($fid)) {
        $query .= " AND $xbbforumstable.xar_fid = ? ";
        $bindvars[] = $fid;
    }
    
    // List of forum IDs
    if (count($fids) > 0) {
        $bindvars = array_merge($bindvars, $fids);
        $query .= " AND $xbbtopicstable.xar_fid IN (?" . str_repeat(',?', count($fids) - 1) . ")";
    }

    // Single topic ID
    if (isset($tid)) {
        $query .= " AND xar_tid = ? ";
        $bindvars[] = (int)$tid;
    }
    
    // List of topic IDs
    if (count($tids) > 0) {
        $bindvars = array_merge($bindvars, $tids);
        $query .= " AND xar_tid IN (?" . str_repeat(',?', count($tids) - 1) . ")";
    }

    // Get by UID
    if (!empty($uid)) {
        $query .= " AND $xbbtopicstable.xar_tposter = ?";
        $bindvars[] = (int)$uid;
    }

    // Get by IP
    if (!empty($ip)) {
        $query .= " AND $xbbtopicstable.xar_thostname = ?";
        $bindvars[] = (string)$ip;
    }

    // Get by timestamp from
    if (!empty($from)) {
        $query .= " AND $xbbtopicstable.xar_ttime >= ? ";
        $bindvars[] = (int)$from;
    }

    // Get by maximum number of replies
    if (isset($maxreplies) && is_numeric($maxreplies)) {
        $query .= " AND $xbbtopicstable.xar_treplies <= ? ";
        $bindvars[] = (int)$maxreplies;
    }

    // Query string words.
    if (!empty($q)) {
        if (empty($qrule)) $rule = 'and';
        if (empty($qarea)) $qarea = 'title';
        $qarea = explode(',', $qarea);
        $qarea = array_flip($qarea);
        $columns = array();
        if (isset($qarea['title'])) $columns[] = 'xar_ttitle';
        if (isset($qarea['post'])) $columns[] = 'xar_tpost';

        // Parse the query string
        $q_parsed = xarModAPIfunc(
            'xarbb', 'user', 'parse_searchwords',
            array('q' => $q, 'columns' => $columns)
        );

        // If we have an additional where-clause, then stick it into the query.
        if (!empty($q_parsed['where'])) {
            $query .= ' AND (' . $q_parsed['where'] . ')';
            $bindvars = array_merge($bindvars, $q_parsed['bind']);
        }
    }

    // No need to sort if just fetching a count.
    if (empty($getcount)) {
        if (empty($sortby)) {
            $sortby = 'time';
        }

        switch ($sortby) {
            case 'poster':
                if (!empty($order) && strtoupper($order) == 'DESC') {
                    $query .= " ORDER BY xar_tposter DESC";
                } else {
                    // default ascending
                    $query .= " ORDER BY xar_tposter ASC";
                }
                break;

            case 'tid':
                if (!empty($order) && strtoupper($order) == 'ASC') {
                    $query .= " ORDER BY xar_tid ASC";
                } else {
                    // default descending
                    $query .= " ORDER BY xar_tid DESC";
                }
                break;

            case 'time':
            default:
                if (!empty($order) && strtoupper($order) == 'ASC') {
                    $query .= " ORDER BY xar_ttime ASC";
                } else {
                    // default descending
                    $query .= " ORDER BY xar_ttime DESC";
                }
                break;
        }
    }

    // Need to run the query and add $numitems to ensure pager works
    if (isset($numitems) && is_numeric($numitems) && empty($getcount)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    } else {
        $result =& $dbconn->Execute($query, $bindvars);
    }
    if (!$result) return;
 
    // TODO: centralise these default settings - used in create() too
    $topic_types = array(
        0 => 'normal',
        1 => 'announce',
        2 => 'sticky',
        3 => 'locked',
        5 => 'shadow'
    );
    $default_options = array(
        'lock' => false,
        'subscribers' => array(),
        'shadow' => NULL,
    );

    // Main topics array.
    $topics = array();

    // For topic tracking.
    $forum_last_visited = array();
    $forum_topic_tracking = array();

    for (; !$result->EOF; $result->MoveNext()) {
        // If fetching a count, then get the first row and exit the loop.
        if (!empty($getcount)) {
            list($topics) = $result->fields;
            break;
        }

        list(
            $tid, $fid, $ttitle, $tpost, $tposter,
            $ttime, $tftime, $treplies, $tstatus, $treplier,
            $toptions,
            $fname, $fdesc, $ftopics,
            $fposts, $fposter, $fpostid, $fstatus, $thostname,
            $catid
        ) = $result->fields;

        if (xarSecurityCheck('ReadxarBB', 0, 'Forum', "$catid:$fid")) {
            $topic = array(
                'tid'     => $tid,
                'fid'     => $fid,
                'ttitle'  => $ttitle,
                'tpost'   => $tpost,
                'tposter' => $tposter,
                'ttime'   => $ttime,
                'tftime'  => $tftime,
                'treplies'=> $treplies,
                'tstatus' => $tstatus,
                'treplier'=> $treplier,
                'toptions'=> $toptions,
                'fname'   => $fname,
                'fdesc'   => $fdesc,
                'ftopics' => $ftopics,
                'fposts'  => $fposts,
                'fposter' => $fposter,
                'fpostid' => $fpostid,
                'fstatus' => $fstatus,
                'catid'   => $catid,
                'thostname' => $thostname,
            );

            //
            // Expand a few items.
            //

            if (!empty($toptions)) {
                $options = unserialize($toptions);
                // Merge the arrays (toptions will overwrite options where keys are the same)
                $options = array_merge($default_options, $options);
            } else {
                $options = $default_options;
            }

            // Set up the array of flags for the topic icons.
            // The icon (or some other flag or visual property) is decided from three different axis:
            // - type = normal/announce/sticky/locked/shadow/unknown (though 'locked' is deprecated as a type)
            // - hot = true/false
            // - new = true/false
            // - locked = true/false
            // Some combinations do not have icons, but we determine that in the display
            // template, not here. These simple flags can replace the topic image number
            // and provide more flexibility to the theme.
            // TODO: include checks on the 'new' flag, which will involve checking
            // details of the current user.
            $topic['icon_flags'] = array();

            $forum = xarModAPIfunc('xarbb', 'user', 'getforum', array('fid' => $fid));
            $hot_topic = $forum['settings']['hottopic'];

            if (isset($topic_types[$topic['tstatus']])) {
                $topic['icon_flags']['type'] = $topic_types[$topic['tstatus']];
            } else {
                $topic['icon_flags']['type'] = 'unknown';
            }
            $topic['icon_flags']['hot'] = (($topic['treplies'] > $hot_topic) ? true : false); 
            $topic['icon_flags']['lock'] = (!empty($topic['options']['lock']) ? true : false);

            //
            // Check whether the topic is 'new' (i.e. unread) or not.
            //
            if (!isset($forum_last_visited[$fid])) {
                // If we don't have topic tracking details for this forum, then fetch it now.
                $forum_last_visited[$fid] = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'f_' . $fid));
                $forum_topic_tracking[$fid] = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'topics_' . $fid));
                if (empty($forum_topic_tracking[$fid])) {
                    $forum_topic_tracking[$fid] = array();
                } else {
                    $forum_topic_tracking[$fid] = unserialize($forum_topic_tracking[$fid]);
                }
            }
            // Topic is new if the tracking array says it is,
            // or has a later timestamp than the last tracked time
            // or it is newer then the last visited time
            if (isset($forum_topic_tracking[$fid][$tid])) {
                $topic['icon_flags']['new'] = 
                    (($forum_topic_tracking[$fid][$tid] == 0 || $forum_topic_tracking[$fid][$tid] < $ttime) ? true : false);
            } else {
                $topic['icon_flags']['new'] = (($ttime > $forum_last_visited[$fid]) ? true : false);
            }

            // Add this topic to the return array.
            $topics[] = $topic;
        }
    }

    $result->Close();

    return $topics;
}

?>