<?php

function xarbb_user_updatetopic()
{

// We need to update the statistics about the forum and the topics here.
// We do this by updating both tables at once and then giving the poster a chance to reply to the 
// topic or go back to the forum of which he came.

    if (!xarVarFetch('tid','int:1:',$tid,10,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modify','int:1:',$modify, 0,XARVAR_NOT_REQUIRED)) return;
    // Start by updating the topic stats.
    
    $uid = xarUserGetVar('uid');

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'updatetopicsview',
                       array('tid'      => $tid,
                             'treplier' => $uid))) return;

    // The user API function is called
    $forum = xarModAPIFunc('xarbb',
                           'user',
                           'gettopic',
                           array('tid' => $tid));
    // Let's not count up if the reply is being edited.
    if ($modify != 1){
        if (!xarModAPIFunc('xarbb',
                           'user',
                           'updateforumview',
                           array('fid'      => $forum['fid'],
                                 'reply'    => 1,
                                 'fposter'  => $uid))) return;
    }

    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    return;
}

?>