<?php
/**
 * File: $Id$
 * 
 * View a list of topics in a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_viewforum()
{
    // Get parameters from whatever input we need
    if(!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('fid', 'id', $fid)) return;

    // The user API function is called.
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $fid));

    if (empty($data)) return;
    if ($data['fstatus'] == 1) {
        $msg = xarML('Forum -- #(1) -- has been locked by administrator', $data['fname']);
        xarExceptionSet(XAR_USER_EXCEPTION, 'LOCKED_FORUM', new SystemException($msg));
        return;
    }
    // Security Check
    if(!xarSecurityCheck('ReadxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;
    xarTplSetPageTitle(xarVarPrepForDisplay($data['fname']));
    // Cookie
    if (isset($_COOKIE["xarbb_all"])){
        $allforumtimecompare = unserialize($_COOKIE["xarbb_all"]);
    } else {
        $allforumtimecompare = '';
    }
    if (isset($_COOKIE["xarbb_f_$fid"])){
        $forumtimecompare = unserialize($_COOKIE["xarbb_f_$fid"]);
    } else {
        $forumtimecompare = '';
    }
    if ($forumtimecompare > $allforumtimecompare){
        $alltimecompare = $forumtimecompare;
    } else {
        $alltimecompare = $allforumtimecompare;
    }

    // Settings and disply
    $data['items']          = array();
    $settings               = unserialize(xarModGetVar('xarbb', 'settings.'.$fid));
    $data['showcats']       = $settings['showcats'];
    $data['xbbname']        = xarModGetVar('themes', 'SiteName');
    // Login
    $data['return_url']      = xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid']));
    $data['submitlabel']     = xarML('Submit');
    // TODO, should be a forum setting
    $hotTopic               = xarModGetVar('xarbb', 'hottopic');

    // The user API function is called
    $topics = xarModAPIFunc('xarbb',
                            'user',
                            'getalltopics',
                            array('fid' => $fid,
                                  'startnum' => $startnumitem,
                                  'numitems' => $settings['topicsperpage']));
    $totaltopics=count($topics);
    for ($i = 0; $i < $totaltopics; $i++) {
        $topic = $topics[$i];
        $topics[$i]['tpost'] = xarVarPrepHTMLDisplay($topic['tpost']);
        $topics[$i]['comments'] = xarVarPrepHTMLDisplay($topic['treplies']);

        // Images
        // Finish our cookie look-up here.
        $tid = $topic['tid'];
        if (isset($_COOKIE["xarbb_t_$tid"])){
            $topictimecompare = unserialize($_COOKIE["xarbb_t_$tid"]);
        } else {
            $topictimecompare = '';
        }
        switch(strtolower($topic['tstatus'])) {
            // Just a regular old topic
            case '0':
            default:

                if (($alltimecompare > $topic['ttime']) || ($topictimecompare > $topic['ttime'])){
                    // More comments than our hottopic setting, therefore should be hot, but not new.
                    if ($topics[$i]['comments'] > $hotTopic){
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_hot.gif') . '" alt="'.xarML('Hot Topic').'" />';
                    // Else should be a regular old boring topic
                    } else {
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New post').'" />';
                    }
                } else {
                    // OOF, look at this topic, hot and new.
                    if ($topics[$i]['comments'] > $hotTopic){
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_new_hot.gif') . '" alt="'.xarML('Hot Topic').'" />';
                    // Else should be a regular old boring topic that has a new post
                    } else {
                        $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_new.gif') . '" alt="'.xarML('New post').'" />';
                    }
                }
                break;
            // Announcement topic
            case '1':

                if (($alltimecompare > $topic['ttime']) || ($topictimecompare > $topic['ttime'])){
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_announce.gif') . '" alt="'.xarML('Announcement').'" />';
                } else {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_announce_new.gif') . '" alt="'.xarML('New Announcement').'" />';
                }

                break;
            // Sticky topic
            case '2':
                if (($alltimecompare > $topic['ttime']) || ($topictimecompare > $topic['ttime'])){
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_sticky.gif') . '" alt="'.xarML('Sticky').'" />';
                } else {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_sticky_new.gif') . '" alt="'.xarML('New Sticky Topic').'" />';
                }
                break;
            // Locked
            case '3':
                $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('No New post').'" />';
                break;
        }

        $topics[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                                'user',
                                                'get',
                                                array('modname' => 'xarbb',
                                                      'itemtype' => 2,
                                                      'objectid' => $topic['tid']));

        if (!$topics[$i]['hitcount']) {
            $topics[$i]['hitcount'] = '0';
        } elseif ($topics[$i]['hitcount'] == 1) {
            $topics[$i]['hitcount'] .= ' ';
        } else {
            $topics[$i]['hitcount'] .= ' ';
        }

        $getname = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                 array('uid' => $topic['tposter']));

        $topics[$i]['name'] = $getname['name'];

        // And we need to know who did the last reply

        if ($topics[$i]['comments'] == 0) {
            $topics[$i]['authorid'] = $topic['tposter'];
        } else {
            // TODO FIX THIS FROM COMMENTS
            $topics[$i]['authorid'] = $topic['treplier'];
        }

        $getreplyname = xarModAPIFunc('roles',
                                      'user',
                                      'get',
                                      array('uid' => $topics[$i]['authorid']));

        $topics[$i]['replyname'] = $getreplyname['name'];
    }
    sort_topics($topics);
    $data['items'] = $topics;

    // Images TODO -- Move these to the template.
    // These are dependant on the time functions being changed
    $data['newtopic']    = '<img src="' . xarTplGetImage('new/post.gif') . '" alt="'.xarML('New topic').'" />';
    $data['newpost']    = '<img src="' . xarTplGetImage('new/folder_new.gif') . '" alt="'.xarML('New post').'" />';
    $data['nonewpost']  = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New post').'" />';
    $data['locked']     = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('No New post').'" />';
    $data['newlocked']     = '<img src="' . xarTplGetImage('new/folder_lock_new.gif') . '" alt="'.xarML('No New post').'" />';
    $data['announcetopic']  = '<img src="' . xarTplGetImage('new/folder_announce.gif') . '" alt="'.xarML('Announcement').'" />';
    $data['newannouncetopic']  = '<img src="' . xarTplGetImage('new/folder_announce_new.gif') . '" alt="'.xarML('New Announcement').'" />';
    $data['hottopic']  = '<img src="' . xarTplGetImage('new/folder_hot.gif') . '" alt="'.xarML('Hot Topic').'" />';
    $data['newhottopic']  = '<img src="' . xarTplGetImage('new/folder_new_hot.gif') . '" alt="'.xarML('New Hot Topic').'" />';
    $data['stickytopic']  = '<img src="' . xarTplGetImage('new/folder_sticky.gif') . '" alt="'.xarML('Sticky Topic').'" />';
    $data['newstickytopic']  = '<img src="' . xarTplGetImage('new/folder_sticky_new.gif') . '" alt="'.xarML('New Sticky Topic').'" />';


    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnumitem,
                                    xarModAPIFunc('xarbb', 'user', 'counttopics', array('fid' => $fid)),
                                    xarModURL('xarbb', 'user', 'viewforum', array('startnumitem' => '%%',
                                                                                  'fid'          => $fid)),
                                    $settings['topicsperpage']);

    // Return the template variables defined in this function
    return $data;
}

/**
 *  Function to help sort the topics array by order of importance
 *  @params $topics array topics to be sorted passed in by reference
 *  @return null
 */
function sort_topics(&$topics)
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
    $topics = array_merge($announcements,$sticky,$normal);
    // get rid of these since we no longer need them in memory
    unset($announcements,$sticky,$normal);
}
?>