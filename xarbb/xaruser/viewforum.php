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

    // Security Check
    if(!xarSecurityCheck('ReadxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

    $data['items'] = array();

    // The user API function is called
    $topics = xarModAPIFunc('xarbb',
                            'user',
                            'getalltopics',
                            array('fid' => $fid,
                                  'startnum' => $startnumitem,
                                  'numitems' => xarModGetVar('xarbb', 'topicsperpage')));
    $totaltopics=count($topics);
    for ($i = 0; $i < $totaltopics; $i++) {
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
    $data['newtopic'] = '<img src="' . xarTplGetImage('newpost.gif') . '" alt="'.xarML('New post').'" />';
    $data['edit']       = '<img src="' . xarTplGetImage('edit.gif') . '" alt="'.xarML('Edit').'" />';
    $data['delete']     = '<img src="' . xarTplGetImage('delete.gif') . '" alt="'.xarML('Delete').'" />';
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" alt="'.xarML('Profile').'" />';
    $data['subscribe']  = '<img src="' . xarTplGetImage('forumsubscribe.gif') . '" alt="'.xarML('Subscribe to this forum').'" />';

    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnumitem,
                                    xarModAPIFunc('xarbb', 'user', 'counttopics', array('fid' => $fid)),
                                    xarModURL('xarbb', 'user', 'viewforum', array('startnumitem' => '%%',
                                                                                  'fid'          => $fid)),
                                    xarModGetVar('xarbb', 'topicsperpage'));

    // Return the template variables defined in this function
    return $data;
}

?>
