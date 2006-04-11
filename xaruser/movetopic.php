<?php
/**
 * Move a forum topic
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // The user API function is called.
    $forum = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $data['fid']));

    if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $forum['catid'] . ':' . $forum['fid'])) return;

    switch(strtolower($phase)) {

        case 'form':
        default:
            // The user API function is called
            $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
            
            // For the dropdown list
            $data['items'] = $forums;
            $data['submitlabel'] = xarML('Submit');
            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':
            if (!xarVarFetch('shadow', 'checkbox', $shadow, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fid', 'int:1:', $fid)) return;
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            // First let's move the topic
            // Actually, lets do this last.  Should not affect anything, but we need to reference 
            // the shadowed topic so we can delete that later if needed.
            /*
            if (!xarModAPIFunc('xarbb',
                            'user',
                            'updatetopic',
                             array('fid'      => $fid,
                                   'tid'      => $tid))) return;
            */
            // Then update the new forum
            if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
            array(
                'fid'      => $fid,
                'tid'      => $tid,
                'ttitle'   => $data['ttitle'],
                'treplies' => $data['treplies'],
                'topics'   => 1,
                'replies'  => $data['treplies'] + 1,
                'move'     => 'positive',
                'fposter'  => $data['tposter'])
                )
            ) return;

            // Get the last topic from the old forum again
            $numtopics = xarModAPIFunc('xarbb', 'user', 'counttopics', array('fid' => $data['fid']));

            if (!empty($numtopics)) {
                $list = xarModAPIFunc('xarbb', 'user', 'getalltopics',
                    array(
                        'fid' => $data['fid'],
                        'startnum' => 1, // already sorted by xar_ttime DESC
                        'numitems' => 1
                    )
                );
                if (!empty($list)) {
                    $last = $list[0];
                    if (!empty($last['treplies'])) {
                        $tposter = $last['treplier'];
                    } else {
                        $tposter = $last['tposter'];
                    }
                } else {
                    $last = array('tid' => 0, 'ttitle' => '', 'treplies' => 0);
                    $tposter = $data['tposter'];
                }
            } else {
                $last = array('tid' => 0, 'ttitle' => '', 'treplies' => 0);
                $tposter = $data['tposter'];
            }
            // Then update the old forum
            if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
                array(
                    'fid'      => $data['fid'],
                    'tid'      => $last['tid'],
                    'ttitle'   => $last['ttitle'],
                    'treplies' => $last['treplies'],
                    'topics'   => 1,
                    'replies'  => $data['treplies'] + 1,
                    'move'     => 'negative',
                    'fposter'  => $tposter)
                )
            ) return;

            // Now let's check to see if there is a shadow post
            if ($shadow != false){
            // Need to create a topic so we don't get the nasty empty error when viewing the forum.
                $ttitle = xarML('Moved') . ' -- ' . $data['ttitle'];
                $tpost = $tid;

                $shadow = xarModAPIFunc('xarbb', 'user', 'createtopic',
                    array(
                        'fid'      => $data['fid'],
                        'ttitle'   => $ttitle,
                        'tpost'    => $tpost,
                        'tposter'  => $data['tposter'],
                        'treplier' => $data['treplier'],
                        'treplies' => $data['treplies'],
                        'tstatus'  => 5
                    )
                ); 

                // Now lets reference this shadow in the old topic.
                // We don't want to kill any subscribers by adding a shadow.
                // I'd give it 2 hours and there would be a bug report for that.
                // Why can't I be lazy sometimes?
                if (!empty($data['toptions'])){
                    $topicoptions = unserialize($data['toptions']);
                    $topicoptions['shadow'] = array();
                    array_push($topicoptions['shadow'], $shadow);
                    $mergedarray = serialize($topicoptions);
                } else {
                    $topicoptions['shadow'] = array($shadow);
                    $mergedarray = serialize($topicoptions);
                }

                if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
                    array(
                        'fid'      => $fid,
                        'ttime'    => $data['ttime'],
                        'toptions' => $mergedarray,
                        'tid'      => $tid)
                    )
                ) return;

            // No Shadow, No Reference
            } else {
                if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
                    array(
                        'fid'      => $fid,
                        'ttime'    => $data['ttime'],
                        'tid'      => $tid)
                    )
                ) return;
            }

            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            break;
    }
    return $data;
}

?>