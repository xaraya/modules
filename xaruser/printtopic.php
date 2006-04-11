<?php
/**
 * Print a forum topic and replies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

function xarbb_user_printtopic($args)
{
   // Get parameters from whatever input we need
    if(!xarVarFetch('tid', 'id', $tid)) return;

    extract($args);
    // redirect to previous/next topic

    if (!$topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid))) return;

    if ($topic['fstatus'] == 1) {
        $msg = xarML('Forum -- #(1) -- all associated topics have been locked by administrator', $topic['fname']);
        xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_FORUM', new SystemException($msg));
        return;
    }


    $settings               = unserialize(xarModGetVar('xarbb', 'settings.' . $topic['fid']));
    $allowhtml              = $settings['allowhtml'];
    if (isset($settings['allowbbcode'])) {
        $allowbbcode = $settings['allowbbcode'];
    } else {
        $allowbbcode = false;
    }

    // Security Check
    if(!xarSecurityCheck('ReadxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;

    // The user API function is called and returns all forum and topic data
    //<jojodee> Do we need to call this again?
    $data = $topic; //to cover us for any use of $data

    // Need to get this working for new itemtypes
    list($data['transformedtext'], $data['transformedtitle']) = xarModCallHooks(
        'item', 'transform', $tid,
        array($data['tpost'], $data['ttitle']),
        'xarbb',
        $data['fid']
    );

    if ($allowhtml) {
        $data['tpost'] = xarVarPrepHTMLDisplay($data['tpost']);
        $data['ttitle'] = xarVarPrepHTMLDisplay($data['ttitle']);
    } else {
        $data['tpost'] = xarVarPrepForDisplay($data['tpost']);
        $data['ttitle'] = xarVarPrepForDisplay($data['ttitle']);
    }

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

    $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
        array(
            'modid'       => $header['modid'],
            'itemtype'    => $data['fid'],
            'objectid'    => $header['objectid']
        )
    );

/*
    $todolist = array();
    $todolist['transform'] = array();
*/

    $totalcomments=count($comments);
    for ($i = 0; $i < $totalcomments; $i++) {
        $comment = $comments[$i];

        if ($allowhtml){
            $comments[$i]['xar_text']=xarVarPrepHTMLDisplay($comments[$i]['xar_text']);
            $comments[$i]['xar_title']=xarVarPrepHTMLDisplay($comments[$i]['xar_title']);
        } else {
            $comments[$i]['xar_text']=xarVarPrepForDisplay($comments[$i]['xar_text']);
            $comments[$i]['xar_title']=xarVarPrepForDisplay($comments[$i]['xar_title']);
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
            'xarbb',
            $data['fid']
        );

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
        $comments[$i]['xar_datestamp'] = $comments[$i]['xar_datetime'];
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

    //Forum Name and Links
    $data['postername'] = $posterdata['name'];
    // $data['posterdate'] = $regdate;
    $data['xbbname']    = xarModGetVar('themes', 'SiteName');

    $item = array();
    $item['module'] = 'xarbb';
    $item['itemtype'] = $data['fid']; // Forum Topics
    $item['itemid'] = $tid;
 

    //pass the bbcodeswitch
    $data['allowbbcode'] = $allowbbcode;

    // Let's suppress the hitcount hook from showing.
    $data['hooks']['hitcount'] = '';
    // Return the template variables defined in this function
    $categories = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid' => $data['catid']));
    $data['catname'] = $categories['name'];
    return $data;
}

?>