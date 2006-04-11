<?php
/**
 * Delete a forum topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
 * @author Jo Dalle Nogare
*/
function xarbb_user_deletetopic()
{
    // Get parameters
    if (!xarVarFetch('tid', 'int:1:', $tid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, $tid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation', 'int', $confirmation, '', XARVAR_NOT_REQUIRED)) return;

    // for sec check
    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
    if (empty($topic)) return;

    // Security Check
    if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        $data['tid'] = $tid;
        return $data;
    } 

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('xarbb', 'admin', 'deletetopics', array('tid' => $tid))) return;
    
    // Let's get rid of the old shadow as well...
    // Was there anything in the toptions field?
    if (!empty($topic['toptions'])){
        // Was that a shadow reference?
        $topicoptions = unserialize($topic['toptions']);
        // OK, question of the day, is there anything in the $shadow var?
        if (!empty($topicoptions['shadow'])) {
            // Kill that shadow as well.
            // $topic options is an array, the shadow element within that also (see movetopic.php around line 138)
            // so we need to pass tids instead of tid (NOTE the s -> see also bug 4047)
            if (!xarModAPIFunc('xarbb', 'admin', 'deletetopics', array('tids' => $topicoptions['shadow']))) return;
        }
    }
    // Blee, Blee, no more shadow, continue with processing.

    // Get the last topic from this forum again
    $numtopics = xarModAPIFunc('xarbb', 'user', 'counttopics', array('fid' => $topic['fid']));
    if (!empty($numtopics)) {
        $list = xarModAPIFunc('xarbb', 'user', 'getalltopics',
            array(
                'fid' => $topic['fid'],
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
            $tposter = xarUserGetVar('uid');
        }
    } else {
        $last = array('tid' => 0, 'ttitle' => '', 'treplies' => 0);
        $tposter = xarUserGetVar('uid');
    }

    if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
        array(
            'fid'      => $topic['fid'],
            'tid'      => $last['tid'],
            'ttitle'   => $last['ttitle'],
            'treplies' => $last['treplies'],
            'topics'   => 1,
            'replies'  => $topic['treplies'],
            'move'     => 'negative',
            'fposter'  => $tposter)
        )
    ) return;

    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum',array('fid' => $topic['fid'])));

    // Return
    return true;
}

?>
