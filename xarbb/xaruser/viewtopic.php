<?php
/**
 * File: $Id$
 * 
 * View a forum topic and replies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

include 'includes/xarDate.php';

function xarbb_user_viewtopic()
{
   // Get parameters from whatever input we need
    if(!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('tid', 'id', $tid)) return;

    //$tid = xarVarCleanFromInput('tid');

    if(!$topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid))) return;    

    // Security Check
    if(!xarSecurityCheck('ReadxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;

    // The user API function is called and returns all forum and topic data
    //<jojodee> Do we need to call this again?
    $data=$topic; //to cover us for any use of $data
  
    //   $data = xarModAPIFunc('xarbb',
    //                          'user',
    //                          'gettopic',
    //                          array('tid' => $tid));


    list($data['transformedtext'],
         $data['transformedtitle']) = xarModCallHooks('item',
                                                      'transform',
                                                       $tid,
                                                 array($data['tpost'],
                                                       $data['ttitle']));

    // The user API function is called
    // <jojodee> Do we need to call this? - is not same data returned in gettopic call above
    //  $forumdata = xarModAPIFunc('xarbb',
    //                              'user',
    //                              'getforum',
    //                               array('fid' => $data['fid']));

    // The user API function is called
    $posterdata = xarModAPIFunc('roles',
                                'user',
                                'get',
                                array('uid' => $data['tposter']));

    //TODO: still need to get tftime and ttime out in proper format 
    // to pass to template for formatting

    // The user API function is called
    $topiccount = xarModAPIFunc('xarbb',
                                'user',
                                'countposts',
                                array('uid' => $data['tposter']));

    // Get the individual posts for the topic
    $header['modid']        = xarModGetIDFromName('xarbb');
    $header['objectid']     = $tid;

    $data['items'] = array();

    // Need to load the renderer since we are making a direct call to the API
    //<jojodee> Do we really need to load this here? Not just for display?
    if (!xarModLoad('comments','renderer')) return;

    //Get posts for each page
    $postsforpage=xarModGetVar('xarbb', 'postsperpage');
    $comments = xarModAPIFunc('comments',
                              'user',
                              'get_multiple',
                              array('modid'       => $header['modid'],
                                    'objectid'    => $header['objectid'],
                                    'startnum' => $startnum,
                                    'numitems' => xarModGetVar('xarbb', 'postsperpage')));

    $totalcomments=count($comments);
    for ($i = 0; $i < $totalcomments; $i++) {
        $comment = $comments[$i];

        list($comments[$i]['xar_text'],
             $comments[$i]['xar_title']) = xarModCallHooks('item',
                                                           'transform',
                                                            $tid,
                                                            array($comment['xar_text'],
                                                                  $comment['xar_title']));

        // The user API function is called
        $comments[$i]['usertopics'] = xarModAPIFunc('xarbb',
                                                    'user',
                                                    'countposts',
                                                    array('uid' => $comment['xar_uid']));

        // The user API function is called
        $comments[$i]['userdata'] = xarModAPIFunc('roles',
                                             'user',
                                             'get',
                                              array('uid' => $comment['xar_uid']));

        //format reply poster's registration date
        $comments[$i]['commenterdate'] = xarLocaleFormatDate('%Y-%m-%d',$comments[$i]['userdata']['date_reg']);
        //Add datestamp so users can format in template, existing templates are still OK
        $comments[$i]['commenterdatestamp'] =$comments[$i]['userdata']['date_reg'];

        //format the post reply date consistently with topic post date
        $comments[$i]['xar_date']=xarLocaleFormatDate('%Y-%m-%d %H:%M:%S',$comments[$i]['xar_datetime']);
        //Add datestamp so users can format in template, existing templates are still OK
        $comments[$i]['xar_datestamp']=$comments[$i]['xar_datetime'];
    }

    $data['items'] = $comments;

    // End individual Replies

    // adjust the display format
    // <jojodee> OK - rather than change each post reply time formats above
    // Let's bring this reg date into line with future locale use
    //$thisdate = new xarDate();
    //if(is_numeric($posterdata['date_reg'])) {
    //    $thisdate->setTimestamp($posterdata['date_reg']);
     $regdate=xarLocaleFormatDate('%Y-%m-%d',$posterdata['date_reg']);
    //Add datestamp so users can format in template, existing templates are still OK
     $regdatestamp=$posterdata['date_reg'];
    //}
    // else {
    //     $thisdate->DBtoTS($posterdata['date_reg']);
    // }

    //Forum Name and Links
    // $data['fname']      = $forumdata['fname']; //No need to reassign here
    $data['postername'] = $posterdata['name'];
    $data['posterdate'] = $regdate;
    $data['posterdatestamp'] = $regdatestamp;
    $data['usertopics'] = $topiccount;
    $data['xbbname']    = xarModGetVar('themes', 'SiteName');
    
    //Pager data - to prevent topic should on every additional pager page
    $data['startnum'] = $startnum;

    //images - add some alt text
    $data['newtopic']   = '<img src="' . xarTplGetImage('newpost.gif') . '" alt="'.xarML('New Topic').'" />';
    $data['emailicon']  = '<img src="' . xarTplGetImage('emailicon.gif') . '" alt="'.xarML('Email').'" />';
    $data['newreply']   = '<img src="' . xarTplGetImage('replypost.gif') . '" alt="'.xarML('New Reply').'" />';
    $data['quote']      = '<img src="' . xarTplGetImage('quote.gif') . '" alt="'.xarML('Quote').'" />';
    $data['edit']       = '<img src="' . xarTplGetImage('edit.gif') . '" alt="'.xarML('Edit').'" />';
    $data['delete']     = '<img src="' . xarTplGetImage('delete.gif') . '" alt="'.xarML('Delete').'" />';
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" alt="'.xarML('Profile').'" />';
    $data['pm']         = '<img src="' . xarTplGetImage('pm.gif') . '" alt="'.xarML('PM').'" />';
    $data['rsstopic']   = '<img src="' . xarTplGetImage('topicsubscribe.gif') . '" alt="'.xarML('Subscribe to this topic').'" />';

    $item = array();
    $item['module'] = 'xarbb';
    $item['itemtype'] = 2; // Forum Topics
    $item['itemid'] = $tid;


    // for display hooks, we need to pass a returnurl
    $item['returnurl'] = xarModURL('xarbb','user','viewtopic',
                                   array('tid' => $tid,
                                         'startnum'=>$startnum));

    $data['hooks'] = xarModCallHooks('item','display',$tid,$item);

    // Let's suppress the hitcount hook from showing.
    $data['hooks']['hitcount'] = '';
    
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    //TODO: Topic is still showing on consecutive Pager pages - works fine on Windows ??
    // short URLs does not make any difference on *nix
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('comments', 'user', 'get_count',
                                    array('modid'       => $header['modid'],
                                          'objectid'    => $header['objectid'])),

                                    xarModURL('xarbb', 'user', 'viewtopic', array('startnum' => '%%',
                                                                                  'tid'          => $tid)),
                                    xarModGetVar('xarbb', 'postsperpage'));

    // Return the template variables defined in this function

    return $data;
}

?>
