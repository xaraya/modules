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
    if (!xarVarFetch('read', 'isset', $read, NULL, XARVAR_DONT_SET)) return;

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

    // Lets deal with the cookie in a more sane manner
    if (isset($read)){
        $time    = serialize(time());
        setcookie(xarModGetVar('xarbb', 'cookiename') . '_f_' . $fid, $time, time()+60*60*24*120, xarModGetVar('xarbb', 'cookiepath'), xarModGetVar('xarbb', 'cookiedomain'), 0);
    }

    // Get the cookie names
    $cookie_name_all_forums_read = xarModGetVar('xarbb', 'cookiename') . '_f_all';
    $cookie_name_this_forum_read = xarModGetVar('xarbb', 'cookiename') . '_f_' . $fid;

    // Cookie
    if (isset($_COOKIE[$cookie_name_all_forums_read])){
        $allforumtimecompare = unserialize($_COOKIE[$cookie_name_all_forums_read]);
    } else {
        $allforumtimecompare = '';
    }
    if (isset($_COOKIE[$cookie_name_this_forum_read])){
        $forumtimecompare = unserialize($_COOKIE[$cookie_name_this_forum_read]);
    } else {
        $forumtimecompare = '';
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
    $hotTopic               = $settings['hottopic'];

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

        // Finish our cookie look-up here.
        $cookie_name_this_topic_read = xarModGetVar('xarbb', 'cookiename') . '_t_' . $topic['tid'];
        if (isset($_COOKIE[$cookie_name_this_topic_read])){
            $topictimecompare = unserialize($_COOKIE[$cookie_name_this_topic_read]);
        } else {
            $topictimecompare = '';
        }

        $cookie_time_compare = max($allforumtimecompare, $forumtimecompare, $topictimecompare);

        switch(strtolower($topic['tstatus'])) {
            // Just a regular old topic
            case '0':
            default:
                if (($cookie_time_compare > $topic['ttime']) || !xarUserIsLoggedIn()){
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

                if (($cookie_time_compare > $topic['ttime']) || !xarUserIsLoggedIn()){
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_announce.gif') . '" alt="'.xarML('Announcement').'" />';
                } else {
                    $topics[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_announce_new.gif') . '" alt="'.xarML('New Announcement').'" />';
                }

                break;
            // Sticky topic
            case '2':
                if (($cookie_time_compare > $topic['ttime']) || !xarUserIsLoggedIn()){
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
                                                      'itemtype' => $topic['fid'],
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

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnumitem,
                                    xarModAPIFunc('xarbb', 'user', 'counttopics', array('fid' => $fid)),
                                    xarModURL('xarbb', 'user', 'viewforum', array('startnumitem' => '%%',
                                                                                  'fid'          => $fid)),
                                    $settings['topicsperpage']);
    $categories = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $data['catid']));
    $data['catname'] = $categories['name'];
//$pre = var_export($data, true); echo "<pre>$pre</pre>"; return;
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