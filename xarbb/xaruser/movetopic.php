<?php
/**
 * File: $Id$
 * 
 * Move a forum topic
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_movetopic()
{
	if (!xarVarFetch('phase', 'str:1:10', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tid','int:1:',$tid)) return;

    // Need to handle locked topics
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'gettopic',
                          array('tid' => $tid));

    // The user API function is called.
    $forum = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $data['fid']));

    if(!xarSecurityCheck('ModxarBB',1,'Forum',$forum['catid'].':'.$forum['fid'])) return;

    switch(strtolower($phase)) {

        case 'form':
        default:
                    // The user API function is called
            $forums = xarModAPIFunc('xarbb',
                                    'user',
                                    'getallforums');
            
            // For the dropdown list
            $data['items'] = $forums;
            $data['submitlabel']    = xarML('Submit');
            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':
            if (!xarVarFetch('shadow','checkbox', $shadow, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fid','int:1:',$fid)) return;
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            // First let's move the topic
            if (!xarModAPIFunc('xarbb',
                            'user',
                            'updatetopic',
                             array('fid'      => $fid,
                                   'tid'      => $tid))) return;

            // Then update the new forum
            if (!xarModAPIFunc('xarbb',
                               'user',
                               'updateforumview',
                               array('fid'      => $fid,
                                     'topics'   => 1,
                                     'replies'  => $data['treplies'] + 1,
                                     'move'     => 'positive',
                                     'fposter'  => $data['tposter']))) return;

            // Then update the old forum
            if (!xarModAPIFunc('xarbb',
                               'user',
                               'updateforumview',
                               array('fid'      => $data['fid'],
                                     'topics'   => 1,
                                     'replies'  => $data['treplies'] + 1,
                                     'move'     => 'negative',
                                     'fposter'  => $data['tposter']))) return;

            // Now let's check to see if there is a shadow post
            if ($shadow != false){
            // Need to create a topic so we don't get the nasty empty error when viewing the forum.
                $ttitle = xarML('Moved') . ' -- ' . $data['ttitle'];
                $tpost = $tid;

                if (!xarModAPIFunc('xarbb',
                                   'user',
                                   'createtopic',
                                   array('fid'      => $data['fid'],
                                         'ttitle'   => $ttitle,
                                         'tpost'    => $tpost,
                                         'tposter'  => $data['tposter'],
                                         'treplier' => $data['treplier'],
                                         'treplies' => $data['treplies'],
                                         'tstatus'  => 5))) return;
            }
            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            break;
    }
    return $data;
}
?>