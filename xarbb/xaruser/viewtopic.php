<?php

function xarbb_user_viewtopic()
{
     
    $tid = xarVarCleanFromInput('tid');

    // Security Check
    if(!xarSecurityCheck('ReadxarBB')) return;
    
    // The user API function is called
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'gettopic',
                          array('tid' => $tid));


    list($data['transformedtext'],
         $data['transformedtitle']) = xarModCallHooks('item',
                                              'transform',
                                              $tid,
                                              array($data['tpost'],
                                                    $data['ttitle']));

    // The user API function is called
    $forumdata = xarModAPIFunc('xarbb',
                               'user',
                               'getforum',
                               array('fid' => $data['fid']));

    // The user API function is called
    $posterdata = xarModAPIFunc('roles',
                                'user',
                                'get',
                                array('uid' => $data['tposter']));

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
    if (!xarModLoad('comments','renderer')) return;

    $comments = xarModAPIFunc('comments',
                              'user',
                              'get_multiple',
                              array('modid'       => $header['modid'],
                                    'objectid'    => $header['objectid']));

    for ($i = 0; $i < count($comments); $i++) {
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

        $comments[$i]['commenterdate'] = $comments[$i]['userdata']['date_reg'];
    }

    $data['items'] = $comments;

    // End individual Replies

    //Forum Name and Links
    $data['fname']      = $forumdata['fname'];
    $data['postername'] = $posterdata['name'];
    $data['posterdate'] = $posterdata['date_reg'];
    $data['usertopics'] = $topiccount;
    $data['xbbname']    = xarModGetVar('themes', 'SiteName');

    //images
    $data['newtopic']   = '<img src="' . xarTplGetImage('newpost.gif') . '" />';
    $data['emailicon']  = '<img src="' . xarTplGetImage('emailicon.gif') . '" />';
    $data['newreply']   = '<img src="' . xarTplGetImage('replypost.gif') . '" />';
    $data['quote']      = '<img src="' . xarTplGetImage('quote.gif') . '" />';
    $data['edit']       = '<img src="' . xarTplGetImage('edit.gif') . '" />';
    $data['delete']     = '<img src="' . xarTplGetImage('delete.gif') . '" />';
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" />';
    $data['pm']         = '<img src="' . xarTplGetImage('pm.gif') . '" />';

    // Return the template variables defined in this function
    return $data;
}

?>