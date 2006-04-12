<?php
/**
 * View a forum topic and replies
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

function xarbb_user_viewtopic($args)
{
   // Get parameters from whatever input we need
    if(!xarVarFetch('startnum', 'id', $startnum,1, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('post', 'str', $post, 2, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('tid', 'id', $tid)) return;
    if(!xarVarFetch('view', 'str', $view,'', XARVAR_NOT_REQUIRED)) return;

    extract($args);
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

    // Session for topic read
    xarSessionSetVar(xarModGetVar('xarbb', 'cookiename') . '_t_' . $tid, time());

    if (!$topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid))) return;

    if ($topic['fstatus'] == 1) {
        $msg = xarML('Forum -- #(1) -- all associated topics have been locked by administrator', $topic['fname']);
        xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_FORUM', new SystemException($msg));
        return;
    }

    // Lets deal with the cookie in a more sane manner
    if (xarUserIsLoggedIn()){
        xarSessionSetVar(xarModGetVar('xarbb', 'cookiename') . '_f_' . $topic['fid'], time());
        xarSessionSetVar(xarModGetVar('xarbb', 'cookiename') . 'lastvisit', time());
    }

    $settings = unserialize(xarModGetVar('xarbb', 'settings.' . $topic['fid']));
    if (isset($settings['allowhtml'])) {
        $allowhtml = $settings['allowhtml'];
    } else {
        $allowhtml = false;
    }
    if (isset($settings['allowbbcode'])) {
        $allowbbcode = $settings['allowbbcode'];
    } else {
        $allowbbcode = false;
    }
    $postperpage = $settings['postsperpage'];

    // Security Check
    if (!xarSecurityCheck('ReadxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;

    // The user API function is called and returns all forum and topic data
    //<jojodee> Do we need to call this again?
    $data = $topic; //to cover us for any use of $data

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
    $data['transformedtitle'] = str_replace("<p>", "", $data['transformedtitle']);
    $data['transformedtitle'] = str_replace("</p>", "", $data['transformedtitle']);
    // End

    xarTplSetPageTitle(xarVarPrepForDisplay($data['ttitle']));
    // The user API function is called
    $posterdata = xarModAPIFunc('roles', 'user', 'get', array('uid' => $data['tposter']));

    // The user API function is called
    $topiccount = xarModAPIFunc('xarbb', 'user', 'countposts', array('uid' => $data['tposter']));

    // Build up the list of posters
    $isposter = array();
    $isposter[$data['tposter']] = 1;

    // Get the individual posts for the topic
    $header['modid']        = xarModGetIDFromName('xarbb');
    $header['objectid']     = $tid;

    $data['items'] = array();

// CHECKME: retrieve from session variable, module user variable or URL param
//          depending on how customisable/cacheable we want to make this ?
//    $postsortby = 'celko'; // unsupported - see below
    if (!empty($settings['postsortorder'])) {
        $postsortorder = $settings['postsortorder'];
    } else {
        $postsortorder = 'ASC';
    }

// TODO: support threaded/nested display too - cfr. bug 1443
//    $postrender = 'flat';

// Note: comments get_multiple() can only return comments in Celko order or reverse Celko order
//       at the moment. This is equivalent to sorting by cid or time here - other postsortby
//       options would require a lot more work, so I would forget about those for now...
    if (!empty($postsortorder) && strtoupper($postsortorder) == 'DESC') {
        $reverse = true;
    } else {
        $reverse = false; // default normal Celko order
    }

    $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
        array(
            'modid'    => $header['modid'],
            'itemtype' => $data['fid'],
            'objectid' => $header['objectid'],
            'startnum' => $startnum,
            'numitems' => $postperpage,
            'reverse'  => $reverse
        )
    );

/*
    $todolist = array();
    $todolist['transform'] = array();
*/

    $totalcomments=count($comments);
    for ($i = 0; $i < $totalcomments; $i++) {
        $comment = $comments[$i];

        if ($allowhtml == true){
            $comment['xar_text'] = xarVarPrepHTMLDisplay($comment['xar_text']);
            $comment['xar_title'] = xarVarPrepHTMLDisplay($comment['xar_title']);
        } else {
            $comment['xar_text'] = xarVarPrepForDisplay($comment['xar_text']);
            $comment['xar_title'] = xarVarPrepForDisplay($comment['xar_title']);
        }

/*
        $todolist['transform'][] = $i . 'xar_text';
        $todolist[$i.'xar_text'] =& $comments[$i]['xar_text'];
        $todolist['transform'][] = $i . 'xar_title';
        $todolist[$i.'xar_title'] =& $comments[$i]['xar_title'];
*/
        // This has to come after the html call.
        list($comments[$i]['xar_text'], $comments[$i]['xar_title']) = xarModCallHooks(
            'item', 'transform', $tid,
            array($comment['xar_text'], $comment['xar_title']),
            'xarbb', $data['fid']
        );
            //Bug 4836 again
            $comments[$i]['xar_title'] = str_replace("<p>", "", $comments[$i]['xar_title']);
            $comments[$i]['xar_title'] = str_replace("</p>", "", $comments[$i]['xar_title']);

// TODO: retrieve all post counts at once ?
        // The user API function is called
        $comments[$i]['usertopics'] = xarModAPIFunc('xarbb', 'user', 'countposts', array('uid' => $comment['xar_uid']));

/*
// TODO: retrieve all user info at once ?
        // The user API function is called
        $comments[$i]['userdata'] = xarModAPIFunc('roles',
                                             'user',
                                             'get',
                                              array('uid' => $comment['xar_uid']));

        //format reply poster's registration date
        //$comments[$i]['commenterdate'] = xarLocaleFormatDate('%Y-%m-%d',$comments[$i]['userdata']['date_reg']);
        //Add datestamp so users can format in template, existing templates are still OK
        $comments[$i]['commenterdatestamp'] =$comments[$i]['userdata']['date_reg'];
*/

        $isposter[$comment['xar_uid']] = 1;

        //format the post reply date consistently with topic post date
        //$comments[$i]['xar_date']=xarLocaleFormatDate('%Y-%m-%d %H:%M:%S',$comments[$i]['xar_datetime']);
        //Add datestamp so users can format in template, existing templates are still OK
        $comments[$i]['xar_datestamp']=$comments[$i]['xar_datetime'];
    }

    $data['posterlist'] = array_keys($isposter);

/*
    $todolist = xarModCallHooks('item',
                                'transform',
                                $tid,
                                $todolist,
                                'xarbb',
                                $data['fid']);
*/

    if (count($data['posterlist']) > 0) {
/* the performance issue seems to be in comments author count, really, so this is not a solution
        $data['usertopics'] = xarModAPIFunc('xarbb','user','countpostslist',
                                            array('uidlist' => $data['posterlist']));
        // TODO: support of legacy templates - get rid of this later on
        for ($i = 0; $i < $totalcomments; $i++) {
            $uid = $comments[$i]['xar_uid'];
            if (isset($data['usertopics'][$uid])) {
                $comments[$i]['usertopics'] = $data['usertopics'][$uid];
            } else {
                $comments[$i]['usertopics'] = 0;
            }
        }
*/
        $data['userdata'] = xarModAPIFunc(
            'roles','user','getall',
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

    // adjust the display format
    // <jojodee> OK - rather than change each post reply time formats above
    // Let's bring this reg date into line with future locale use
    //$thisdate = new xarDate();
    //if(is_numeric($posterdata['date_reg'])) {
    //    $thisdate->setTimestamp($posterdata['date_reg']);
    // $regdate=xarLocaleFormatDate('%Y-%m-%d',$posterdata['date_reg']);
    //Add datestamp so users can format in template, existing templates are still OK
    $regdatestamp=$posterdata['date_reg'];
    //}
    // else {
    //     $thisdate->DBtoTS($posterdata['date_reg']);
    // }

    //Forum Name and Links
    // $data['fname']      = $forumdata['fname']; //No need to reassign here
    $data['postername'] = $posterdata['name'];
    // $data['posterdate'] = $regdate;
    $data['posterdatestamp'] = $regdatestamp;
    $data['usertopics'] = $topiccount;
    $data['xbbname']    = xarModGetVar('themes', 'SiteName');
    
    //Pager data - to prevent topic should on every additional pager page
    $data['startnum'] = $startnum;

    // Images
    // These are dependant on the time functions being changed
    $data['post']       = $post;

    $item = array();
    $item['module'] = 'xarbb';
    $item['itemtype'] = $data['fid']; // Forum Topics
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

    //pass the bbcodeswitch
    $data['allowbbcode'] = $allowbbcode;
    //pass the htmlmod switch
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
        $postperpage
    );

    // Return the template variables defined in this function
    $categories = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $data['catid']));
    $data['catname'] = $categories['name'];

    // Forum Jump
    $data['forums'] = xarModAPIFunc('xarbb', 'user', 'getallforums');
    
    // Lets check our options as well for a dual status topic
    if (!empty($topic['toptions'])){
        $topicoptions = unserialize($data['toptions']);
        // OK, just need to trick the topic now if the conditions are set.
        if (!empty($topicoptions['lock'])){
            $data['tstatus'] = 3;
        }
        // Check if we subscribed already
        if (xarUserIsLoggedIn()) {
            $uid = (int)xarUserGetVar('uid');
            if (!empty($topicoptions['subscribers']) && in_array($uid, $topicoptions['subscribers'])) {
                $data['tsubscribed'] = 1;
            }
        }
    }

    return $data;
}

?>
