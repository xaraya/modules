<?php
/**
 * Delete a forum reply
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

function xarbb_user_deletereply()
{
    // Get parameters
    if (!xarVarFetch('cid','int:1:',$cid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$cid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirmation','int',$confirmation,'',XARVAR_NOT_REQUIRED)) return;

    // for sec check
    if (!$comment = xarModAPIFunc('comments', 'user', 'get_one', array('cid' => $cid))) return;
    $tid = $comment[0]['xar_objectid'];

    if (!$topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid))) return;

    // Security Check
    if(!xarSecurityCheck('ModxarBB', 1, 'Forum', $topic['catid'].':'.$topic['fid'])) return;
 
    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        $data['cid'] = $cid;
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('xarbb', 'admin', 'deletereplies', array('cid' => $cid))) return;

    if (!empty($topic['treplies'])) {
        $topic['treplies'] = $topic['treplies'] - 1;
    } else {
        $topic['treplies'] = 0;
    }

    // Need to update the forum page to show one less reply
    if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
        array(
            'fid'      => $topic['fid'],
            'replies'  => 1,
            'move'     => 'negative',
            'fposter'  => $topic['tposter'],
            'tid'      => $tid,
            'ttitle'   => $topic['ttitle'],
            'treplies' => $topic['treplies'])
        )
    ) return;
 
    // Redirect
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    // Return
    return true;
}
?>
