<?php

function xarbb_user_viewforum()
{
     
    $fid = xarVarCleanFromInput('fid');

    $data['items'] = array();

    // Security Check
    if(!xarSecurityCheck('ReadxarBB')) return;

    // The user API function is called
    $topics = xarModAPIFunc('xarbb',
                            'user',
                            'getalltopics',
                            array('fid' => $fid));

    for ($i = 0; $i < count($topics); $i++) {
        $topic = $topics[$i];

        $topics[$i]['comments'] = xarVarPrepForDisplay($topic['treplies']);
        
        // While we are here, lets do the hot topics, etc.
        $redhotTopic    = xarModGetVar('xarbb', 'redhottopic');
        $hotTopic       = xarModGetVar('xarbb', 'hottopic');
        
        if (($topics[$i]['comments']) >= ($hotTopic)){
            $topics[$i]['folder']       = '<img src="' . xarTplGetImage('hot_folder.gif') . '" />';
        } else if (($topics[$i]['comments']) >= ($redhotTopic)){
            $topics[$i]['folder']       = '<img src="' . xarTplGetImage('hot_red_folder.gif') . '" />';
        } else {
            $topics[$i]['folder']       = '<img src="' . xarTplGetImage('folder.gif') . '" />';
        }

        $topics[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                                'user',
                                                'get',
                                                array('modname' => 'xarbb',
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
    
    $forums = xarModAPIFunc('xarbb',
                            'user',
                            'getforum',
                            array('fid' => $fid));

    //Forum Name
    $data['xbbname']    = xarModGetVar('themes', 'SiteName');

    // Add the array of items to the template variables
    $data['fid'] = $fid;
    $data['items'] = $topics;
    $data['fname'] = $forums['fname'];

    //images
    $data['newtopic'] = '<img src="' . xarTplGetImage('newpost.gif') . '" />';
    
    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;
}

?>