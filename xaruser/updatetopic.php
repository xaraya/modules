<?php

/**
 * Update a forum topic
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 * @author Jo dalle Nogare
 */
/**
 * Update a topic with a new reply
 *
 * @param int tid topic id
 * @param int modify
 * @return array
 */

function xarbb_user_updatetopic()
{
    // We need to update the statistics about the forum and the topics here.
    // We do this by updating both tables at once and then giving the poster a chance to reply to the
    // topic or go back to the forum of which he came.

    if (!xarVarFetch('tid', 'id', $tid)) return;
    if (!xarVarFetch('modify', 'int:0:1', $modify, 0, XARVAR_NOT_REQUIRED)) return;

    // Need to handle locked topics
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // TODO: the locked status is now a topic option, not a main status
    if ($data['tstatus'] == 3) {
        $msg = xarML('Topic -- #(1) -- has been locked by administrator', $data['ttitle']);
        xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_TOPIC', new SystemException($msg));
        return;
    }

    // get the number of comments
    // Need to move this outside the modify condition so we can return to the topic
    // Bug 3517
    // Start by updating the topic stats.
    $modid = xarModGetIDFromName('xarbb');
    $count = xarModAPIFunc('comments', 'user', 'get_count',
        array(
            'modid'       => $modid,
            'itemtype'    => $data['fid'],
            'objectid'    => $tid
        )
    );

    // Don't count up if the topic is being edited? Need to add modify test here.
    if ($modify != 1) {
        // get the last comment
        $comments = xarModAPIFunc('comments', 'user', 'get_multiple',
            array(
                'modid'       => $modid,
                'itemtype'    => $data['fid'],
                'objectid'    => $tid,
                'startnum' => $count,
                'numitems' => 1
            )
        );

        $totalcomments = count($comments);
        $isanon = $comments[$totalcomments-1]['xar_postanon'];
        $anonuid = xarConfigGetVar('Site.User.AnonymousUID');

        if ($isanon == 1) {
            $poster = $anonuid;
        } else {
            $poster = xarUserGetVar('uid');
        }

        if (!xarModAPIFunc('xarbb', 'user', 'updatetopicsview',
            array('tid' => $tid, 'treplies' => $count, 'treplier' => $poster)
        )) return;
       
        if (!xarModAPIFunc('xarbb', 'user', 'updateforumview',
            array(
                'fid'      => $data['fid'],
                'tid'      => $tid,
                'ttitle'   => $data['ttitle'],
                'treplies' => $count,
                'replies'  => 1,
                'move'     => 'positive',
                'fposter'  => $poster
            )
        )) return;

        // While we are here, let's send any subscribers notifications.
        // TODO: provide an option to queue the notificiations, because if there are lot of
        // subscribers, we don't want to delay the posting of a reply while the e-mails are sent.
        if (!xarModAPIFunc('xarbb', 'user', 'replynotify', array('tid' => $tid))) return;
    }

    $forumreturn = xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid']));
    $replyreturn = xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid, 'startnum' => $count));
    $topicreturn = xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid));
    $xarbbtitle = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $xarbbtitle = (isset($xarbbtitle) ? $xarbbtitle : '');

    $data = xarTplModule('xarbb', 'user', 'return',
        array(
            'forumreturn'   => $forumreturn,
            'topicreturn'   => $topicreturn,
            'replyreturn'   => $replyreturn,
            'xarbbtitle'    => $xarbbtitle
        )
    );

    return $data;
}

?>