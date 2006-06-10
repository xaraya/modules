<?php

/**
 * View a forum topic and replies
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage  xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 * @author Jo dalle Nogare
*/

function xarbb_user_viewtopic($args)
{
   // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum,1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('post', 'str', $post, 2, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'id', $tid)) return;
    if (!xarVarFetch('view', 'enum:next:previous:prev', $view, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    $now = time();

    // redirect to previous/next topic
    if (!empty($view)) {
        if ($view == 'next') {
            $nextid = xarModAPIFunc('xarbb', 'user', 'getnexttopicid', array('tid' => $tid));
            if (!isset($nextid)) return;
            if (!empty($nextid)) {
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $nextid)));
                return true;
            }
        } elseif ($view == 'previous' || $view == 'prev') {
            $previousid = xarModAPIFunc('xarbb', 'user', 'getprevioustopicid', array('tid' => $tid));
            if (!isset($previousid)) return;
            if (!empty($previousid)) {
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $previousid)));
                return true;
            }
        }
    }

    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
    // TODO: redirect to a nicer error page within xarBB if the topic does not exist.
    if (empty($topic)) return xarModFunc('xarbb', 'user', 'showerror', array('errortype' => 'NOTOPIC', 'tid' => $tid));

    $fid = $topic['fid'];

    // Fetch the topic tracking array for this forum.
    $topic_tracking = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'topics_' . $fid));
    if (empty($topic_tracking)) {
        $topic_tracking = array();
    } else {
        $topic_tracking = unserialize($topic_tracking);
    }

    // If this topic is in the array, then set the last visited time.
    // If it is not in the array, then it was never marked as 'unread'
    // and so can be safely ignored.
    if (isset($topic_tracking[$tid])) {
        $topic_tracking[$tid] = $topic['ttime'];

        // Store the topic tracking array for this forum (only bother if we have changed it).
        // No need to sort and truncate it as we are not adding anything to it.
        xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'topics_' . $fid, 'value' => serialize($topic_tracking)));
    }

    // TODO: centralise this ('locked' error appears in many places)
    if ($topic['fstatus'] == 1) {
        $msg = xarML('Forum -- #(1) -- all associated topics have been locked by administrator', $topic['fname']);
        xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_FORUM', new SystemException($msg));
        return;
    }

    // Store the last visited times.
    // TODO: put the forums into one array, so we don't need to create module
    // variables for each new forum.
    xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'f_' . $fid, 'value' => $now));
    xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'lastvisit', 'value' => $now));

    $forum = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    $settings = $forum['settings'];
    $allowhtml = $settings['allowhtml'];
    $allowbbcode = $settings['allowbbcode'];
    $postsperpage = $settings['postsperpage'];

    $catid=(int)$forum['catid'];

    // Security Check
    if (!xarSecurityCheck('ReadxarBB', 1, 'Forum', "$catid:$fid")) return;

    // Data for the template.
    $data = $topic;

    $data['pager'] = '';

    if ($allowhtml == true) {
        $data['tpost'] = xarVarPrepHTMLDisplay($data['tpost']);
        $data['ttitle'] = xarVarPrepHTMLDisplay($data['ttitle']);
    } else {
        $data['tpost'] = xarVarPrepForDisplay($data['tpost']);
        $data['ttitle'] = xarVarPrepForDisplay($data['ttitle']);
    }

    // Need to get this working for new itemtypes
    list($data['transformedtext'], $data['transformedtitle']) = xarModCallHooks(
        'item', 'transform', $tid,
        array($data['tpost'], $data['ttitle']),
        'xarbb', $data['fid']
    );

    // Bug 4836
    // FIXME: fix this at source, not a hack here.
    $data['transformedtitle'] = str_replace("<p>", "", $data['transformedtitle']);
    $data['transformedtitle'] = str_replace("</p>", "", $data['transformedtitle']);
    // End

    xarTplSetPageTitle($data['ttitle']);

    // The user API function is called
    $posterdata = xarModAPIFunc('roles', 'user', 'get', array('uid' => $data['tposter']));

    // The user API function is called
    $topiccount = xarModAPIFunc('xarbb', 'user', 'countposts', array('uid' => $data['tposter']));

    // Build up the list of posters
    $isposter = array();
    $isposter[$data['tposter']] = 1;

    // Get the individual posts for the topic
    $header['modid'] = xarModGetIDFromName('xarbb');
    $header['objectid'] = $tid;

    $data['items'] = array();

    // CHECKME: retrieve from session variable, module user variable or URL param
    //          depending on how customisable/cacheable we want to make this ?
    //    $postsortby = 'celko'; // unsupported - see below
    if (!empty($settings['postsortorder'])) {
        $postsortorder = $settings['postsortorder'];
    } else {
        $postsortorder = 'ASC';
    }

    // Note: 
    // comments get_multiple() can only return comments in Celko order or reverse Celko order
    // at the moment. This is equivalent to sorting by cid or time here - other postsortby
    // options would require a lot more work, so I would forget about those for now...
    // ...really?
    if (!empty($postsortorder) && strtoupper($postsortorder) == 'DESC') {
        $reverse = true;
    } else {
        $reverse = false; // default normal Celko order
    }

    //
    // TODO: Fetch the replies from getallreplies(), once that has been implemented.
    //

    $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
        array(
            'modid'    => $header['modid'],
            'itemtype' => $data['fid'],
            'objectid' => $header['objectid'],
            'startnum' => $startnum,
            'numitems' => $postsperpage,
            'reverse'  => $reverse
        )
    );

    $totalcomments = count($comments);
    for ($i = 0; $i < $totalcomments; $i++) {
        $comment = $comments[$i];

        if ($allowhtml == true){
            $comment['xar_text'] = xarVarPrepHTMLDisplay($comment['xar_text']);
            $comment['xar_title'] = xarVarPrepHTMLDisplay($comment['xar_title']);
        } else {
            $comment['xar_text'] = xarVarPrepForDisplay($comment['xar_text']);
            $comment['xar_title'] = xarVarPrepForDisplay($comment['xar_title']);
        }

        // This has to come after the html call.
        list($comments[$i]['xar_text'], $comments[$i]['xar_title']) = xarModCallHooks(
            'item', 'transform', $tid,
            array($comment['xar_text'], $comment['xar_title']),
            'xarbb', $data['fid']
        );

        // Bug 4836 again
        // FIXME: fix this at source, not a hack here.
        $comments[$i]['xar_title'] = str_replace("<p>", "", $comments[$i]['xar_title']);
        $comments[$i]['xar_title'] = str_replace("</p>", "", $comments[$i]['xar_title']);

        // TODO: retrieve all post counts at once?
        // The user API function is called
        $comments[$i]['usertopics'] = xarModAPIFunc('xarbb', 'user', 'countposts', array('uid' => $comment['xar_uid']));

        $isposter[$comment['xar_uid']] = 1;

        //format the post reply date consistently with topic post date
        //$comments[$i]['xar_date']=xarLocaleFormatDate('%Y-%m-%d %H:%M:%S',$comments[$i]['xar_datetime']);
        //Add datestamp so users can format in template, existing templates are still OK
        $comments[$i]['xar_datestamp']=$comments[$i]['xar_datetime'];
    }

    $data['posterlist'] = array_keys($isposter);

    if (count($data['posterlist']) > 0) {
        $data['userdata'] = xarModAPIFunc(
            'roles', 'user', 'getall',
            array('order' => 'uid', 'uidlist' => $data['posterlist'])
        );

        for ($i = 0; $i < $totalcomments; $i++) {
            $uid = $comments[$i]['xar_uid'];
            if (isset($data['userdata'][$uid])) {
                $comments[$i]['commenterdatestamp'] = $data['userdata'][$uid]['date_reg'];
            } else {
                $comments[$i]['commenterdatestamp'] = 0;
            }
        }
    }

    $data['items'] = $comments;

    // End individual Replies

    // Add datestamp so users can format in template, existing templates are still OK
    $regdatestamp = $posterdata['date_reg'];

    // Forum Name and Links
    $data['postername'] = $posterdata['name'];

    // $data['posterdate'] = $regdate;
    $data['posterdatestamp'] = $regdatestamp;
    $data['usertopics'] = $topiccount;
    $data['xbbname'] = xarModGetVar('themes', 'SiteName');

    //Pager data - to prevent topic should on every additional pager page
    $data['startnum'] = $startnum;

    // Images
    // These are dependant on the time functions being changed
    $data['post'] = $post;

    $item = array();
    $item['module'] = 'xarbb';
    $item['itemtype'] = $data['fid'];
    $item['itemid'] = $tid;


    // for display hooks, we need to pass a returnurl
    $item['returnurl'] = xarModURL('xarbb', 'user', 'viewtopic',
        array('tid' => $tid, 'startnum' => $startnum)
    );

    $data['hooks'] = xarModCallHooks('item', 'display', $tid, $item);

    // Let's handle the changelog a little differently
    // and add a link in the topic itself.
    if (isset($data['hooks']['changelog'])){
        $data['changelog'] = true;
        $data['hooks']['changelog'] = '';
    }

    // Pass the bbcodeswitch
    $data['allowbbcode'] = $allowbbcode;

    // Pass the htmlmod switch
    $data['allowhtml'] = $allowhtml;

    // Let's suppress the hitcount hook from showing.
    $data['hooks']['hitcount'] = '';

    // Generate authid only if the current user can delete topics and replies
    if (xarSecurityCheck('DeletexarBB', 0)) {
        // Note : this make the page un-cacheable
        $data['authid'] = xarSecGenAuthKey('xarbb');
    } else {
        $data['authid'] = '';
    }

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager(
        $startnum, $topic['treplies'],
        xarModURL('xarbb', 'user', 'viewtopic', array('startnum' => '%%', 'tid' => $tid)),
        $postsperpage
    );

    // Return the template variables defined in this function
    $categories = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $data['catid']));
    $data['catname'] = $categories['name'];

    // Forum Jump
    $data['forums'] = xarModAPIFunc('xarbb', 'user', 'getallforums');

    // Lets check our options as well for a dual status topic
    if (!empty($topic['options'])) {

        // OK, just need to trick the topic now if the conditions are set.
        // TODO: fix this - we don't want to hide the real status (i.e. 'type') of the topic
        // behind the fact that it is locked.
        if (!empty($data['options']['lock'])){
            $data['tstatus'] = 3;
        }

        // Check if we subscribed already
        // Tell the template:
        //  1 = subscribed
        //  0 = not subscribed
        if (xarUserIsLoggedIn()) {
            $uid = (int)xarUserGetVar('uid');
            if (!empty($data['options']['subscribers']) && in_array($uid, $data['options']['subscribers'])) {
                $data['tsubscribed'] = 1;
            } else {
                $data['tsubscribed'] = 0;
            }

        } else {
            // Not logged in, so cannot be subscribed.
            $data['tsubscribed'] = 0;
        }
    } else {
        $data['tsubscribed'] = 0;
    }

    // User needs post permission on this topic in order to subscribe.
    // TODO: is this a necessary restriction? Why not subscribe to a read-only topic?
    // The APIs dictate this.
    if (xarSecurityCheck('PostxarBB', 0, 'Forum', $topic['catid'] . ':' . $topic['fid'])) {
        $data['tsubrights'] = 1;
    } else {
        $data['tsubrights'] = 0;
    }

    return $data;
}

?>