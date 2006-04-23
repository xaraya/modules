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
 * @fixme There is a lot that needs updating in this script, to bring into line with other scripts.
*/

function xarbb_user_searchtopics()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('by', 'id', $uid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replies', 'int:0', $replies, NULL, XARVAR_NOT_REQUIRED)) return;
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

    // The user API function is called
    // TODO: how many APIs? Could we merge all these into one?
    if (!empty($uid)) {
        $data['message'] = xarML('Your topics');
        $topics = xarModAPIFunc(
            'xarbb', 'user', 'getalltopics_byuid',
            array('uid' => $uid, 'startnum' => $startnumitem, 'numitems' => $topicsperpage)
        );
    } elseif (isset($replies)) {
        $data['message'] = xarML('Unanswered topics');
        if (!isset($fid)) {
            $topics = xarModAPIFunc(
                'xarbb', 'user', 'getalltopics_byunanswered',
                array('startnum' => $startnumitem, 'numitems' => $topicsperpage)
            );
        } else {
            $topics = xarModAPIFunc(
                'xarbb', 'user', 'getalltopics_byunanswered',
                array('fid' => $fid, 'startnum' => $startnumitem, 'numitems' => $topicsperpage)
            );
        }
    } elseif (!empty($from)) {
        $data['message'] = xarML('Topics since your last visit');
        $topics = xarModAPIFunc(
            'xarbb', 'user', 'getalltopics_bytime',
            array('from' => $from, 'startnum' => $startnumitem, 'numitems' => $topicsperpage)
        );
    } elseif (!empty($ip)) {
        $data['message'] = xarML('Topics posted from IP address');
        $topics = xarModAPIFunc(
            'xarbb', 'user', 'getalltopics_byip',
            array('ip' => $ip, 'startnum' => $startnumitem, 'numitems' => $topicsperpage)
        );
    }

    $totaltopics = count($topics);

    for ($i = 0; $i < $totaltopics; $i++) {
        $topic = $topics[$i];

        $topics[$i]['tpost'] = $topic['tpost'];
        $topics[$i]['comments'] = $topic['treplies'];

        $fid = $topic['fid'];
        $tid = $topic['tid'];

        // Check to see if forum is locked
        // TODO: check the 'lock' option value.
        if ($topic['fstatus'] == 1) {
            // TODO: move markup to the template
            $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('Forum Locked').'" />';
        } else {
            $cookie_all = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'lastvisit'));
            if (!empty($cookie_all)) {
                $allforumtimecompare = $cookie_all;
            } else {
                $allforumtimecompare = '';
            }

            $cookie_f = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'f_' . $fid));
            if (!empty($cookie_f)) {
                $forumtimecompare = $cookie_f;
            } else {
                $forumtimecompare = '';
            }

            if ($forumtimecompare > $allforumtimecompare){
                $alltimecompare = $forumtimecompare;
            } else {
                $alltimecompare = $allforumtimecompare;
            }

            $tid = $topic['tid'];

            // FIXME: Check the topic tracking arrays to get the read status
            $topictimecompare = '';
        }
        
        // FIXME: the 'viewtopic' script does it differently, and without the markup - copy code over.
        switch(strtolower($topic['tstatus'])) {
            // Just a regular old topic
            case '0':
            default:
                // FIXME: move all this markup to the templates
                if (($alltimecompare > $topic['ttime']) || ($topictimecompare > $topic['ttime'])) {
                    // More comments than our hottopic setting, therefore should be hot, but not new.
                    if ($topics[$i]['comments'] > $hotTopic){
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_hot.gif') . '" alt="'.xarML('Hot Topic').'" />';
                    // Else should be a regular old boring topic
                    } else {
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New posts').'" />';
                    }
                } else {
                    // OOF, look at this topic, hot and new.
                    if ($topics[$i]['comments'] > $hotTopic) {
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_new_hot.gif') . '" alt="'.xarML('Hot Topic').'" />';
                    // Else should be a regular old boring topic that has a new post
                    } else {
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_new.gif') . '" alt="'.xarML('New post').'" />';
                    }
                }
                break;

            // Announcement topic
            case '1':
                if (($alltimecompare > $topic['ttime']) || ($topictimecompare > $topic['ttime'])) {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_announce.gif') . '" alt="'.xarML('Announcement').'" />';
                } else {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_announce_new.gif') . '" alt="'.xarML('New Announcement').'" />';
                }

                break;
            // Sticky topic
            case '2':
                if (($alltimecompare > $topic['ttime']) || ($topictimecompare > $topic['ttime'])) {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_sticky.gif') . '" alt="'.xarML('Sticky').'" />';
                } else {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_sticky_new.gif') . '" alt="'.xarML('New Sticky Topic').'" />';
                }
                break;
            // Locked
            case '3':
                $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('No New posts').'" />';
                break;
        }


        
        if ($topics[$i]['comments'] == 0) {
            $topics[$i]['authorid'] = $topic['tposter'];
        } else {
            // TODO FIX THIS FROM COMMENTS
            $topics[$i]['authorid'] = $topic['treplier'];
        }

        $getreplyname = xarModAPIFunc('roles', 'user', 'get', array('uid' => $topics[$i]['authorid']));

        $topics[$i]['replyname'] = $getreplyname['name'];

        $topics[$i]['hitcount'] = xarModAPIFunc('hitcount', 'user', 'get',
            array('modname' => 'xarbb', 'itemtype' => $topic['fid'], 'objectid' => $topic['tid'])
        );

        if (!$topics[$i]['hitcount']) {
            $topics[$i]['hitcount'] = '0';
        } elseif ($topics[$i]['hitcount'] == 1) {
            $topics[$i]['hitcount'] .= ' ';
        } else {
            $topics[$i]['hitcount'] .= ' ';
        }

        $getname = xarModAPIFunc('roles', 'user', 'get', array('uid' => $topic['tposter']));

        $topics[$i]['name'] = $getname['name'];

    }

    // Initialize some vars for search
    $where='';
    $wherevalue='';
    $data['items'] = $topics;
    $data['totalitems'] = $totaltopics;
    if ($totaltopics > 0) { //only do this if we need to page else don't worry about it ;)
        if (!empty($uid)) {
            $data['pager'] = xarTplGetPager(
                $startnumitem,
                xarModAPIFunc('xarbb', 'user', 'counttotaltopics', array('where' =>'uid', 'wherevalue' => $uid)),
                xarModURL('xarbb', 'user', 'searchtopics', array('startnumitem' => '%%', 'by' => $uid)),
                $topicsperpage
            );
        } elseif (!empty($replies)) {
            $data['pager'] = xarTplGetPager(
                $startnumitem,
                xarModAPIFunc('xarbb', 'user', 'counttotaltopics', array('wherevalue' => $replies, 'where' => 'replies')),
                xarModURL('xarbb', 'user', 'searchtopics', array('startnumitem' => '%%', 'replies' => $replies)),
                $topicsperpage
            );
        } elseif (!empty($from)) {
            $data['pager'] = xarTplGetPager(
                $startnumitem,
                xarModAPIFunc('xarbb', 'user', 'counttotaltopics',array('wherevalue' => $from,'where' => 'from')),
                xarModURL('xarbb', 'user', 'searchtopics', array('startnumitem' => '%%', 'from' => $from)),
                $topicsperpage
            );
        }
    }

    $xarbbtitle = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $data['xarbbtitle'] = (isset($xarbbtitle) ? $xarbbtitle : '');
    
    return $data;

}

?>