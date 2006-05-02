<?php

/**
 * View a list of topics in a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @fixme We can mix and match different search methods, but the 'message' will be the last method chosen only.
 */

function xarbb_user_searchtopics()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('by', 'id', $uid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replies', 'int:0:1', $replies, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from', 'int:1', $from, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ip', 'str:1:20', $ip, NULL, XARVAR_NOT_REQUIRED)) return;

    // Security check probably not good enough as is.
    // TODO: we need to check the security on the forum itself - the fid and the cid
    if (!xarSecurityCheck('ReadxarBB')) return;
    
    // TODO: we have settings for the individual forum.
    $xarsettings= xarModGetVar('xarbb', 'settings');
    if (!empty($xarsettings)) {
        $settings = unserialize($xarsettings);
    }
    $topicsperpage = (!isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage']);

    $data['items'] = array();
    $hotTopic = xarModGetVar('xarbb', 'hottopic');

    $search_args = array();
    $search_args['startnum'] = $startnum;
    $search_args['numitems'] = $topicsperpage;

    $data['message'] = xarML('All topics');

    if (!empty($fid)) {
        $data['message'] = xarML('Topics for a forum');
        $search_args['fid'] = $fid;
    }

    if (!empty($uid)) {
        $data['message'] = xarML('Topics for a user');
        $search_args['uid'] = $uid;
    }
    
    if (!empty($replies)) {
        $data['message'] = xarML('Unanswered topics');
        $search_args['maxreplies'] = 0;
    }
    
    if (!empty($from)) {
        $data['message'] = xarML('Topics since your last visit');
        $search_args['from'] = $from;
    }
    
    if (!empty($ip)) {
        $data['message'] = xarML('Topics posted from IP address');
        $search_args['ip'] = $ip;
    }

    // Fetch all matching topics.
    $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics', $search_args);

    // This is just the total topics on the page.
    $totaltopics = count($topics);

    for ($i = 0; $i < $totaltopics; $i++) {
        $topic = $topics[$i];

        $topics[$i]['tpost'] = $topic['tpost'];
        $topics[$i]['comments'] = $topic['treplies'];

        // Not sure what this does.
        if ($topics[$i]['comments'] == 0) {
            $topics[$i]['authorid'] = $topic['tposter'];
        } else {
            // TODO FIX THIS FROM COMMENTS
            $topics[$i]['authorid'] = $topic['treplier'];
        }

        $topics[$i]['replyname'] = xarUserGetVar('name', $topics[$i]['authorid']);

        $topics[$i]['hitcount'] = xarModAPIFunc('hitcount', 'user', 'get',
            array('modname' => 'xarbb', 'itemtype' => $topic['fid'], 'objectid' => $topic['tid'])
        );

        if (empty($topics[$i]['hitcount'])) {
            $topics[$i]['hitcount'] = '0';
        } elseif ($topics[$i]['hitcount'] == 1) {
            $topics[$i]['hitcount'] .= ' ';
        } else {
            $topics[$i]['hitcount'] .= ' ';
        }

        $topics[$i]['name'] = xarUserGetVar('name', $topic['tposter']);
    }

    // Initialize some vars for search
    $data['items'] = $topics;
    $data['totalitems'] = $totaltopics;

    if ($totaltopics > 0) {
        // Get a count of all topics matching the search criteria.
        $search_args['getcount'] = true;
        $topiccount = xarModAPIFunc('xarbb', 'user', 'getalltopics', $search_args);

        $pager_args = array('startnum' => '%%');

        if (!empty($fid)) $pager_args['fid'] = $fid;
        if (!empty($uid)) $pager_args['by'] = $uid;
        if (!empty($replies)) $pager_args['replies'] = $replies;
        if (!empty($ip)) $pager_args['ip'] = $ip;
        if (!empty($from)) $pager_args['from'] = $from;

        $data['pager'] = xarTplGetPager(
            $startnum, $topiccount,
            xarModURL('xarbb', 'user', 'searchtopics', $pager_args),
            $topicsperpage
        );
    }

    $xarbbtitle = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $data['xarbbtitle'] = (isset($xarbbtitle) ? $xarbbtitle : '');
    
    return $data;
}

?>