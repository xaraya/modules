<?php
/**
 * File: $Id$
 * 
 * Update a forum topic
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_updatetopic()
{
// We need to update the statistics about the forum and the topics here.
// We do this by updating both tables at once and then giving the poster a chance to reply to the
// topic or go back to the forum of which he came.

  if (!xarVarFetch('tid','int:1:',$tid,$tid,XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('modify','int:1:',$modify, 0,XARVAR_NOT_REQUIRED)) return;

    // Start by updating the topic stats.
    $modid        = xarModGetIDFromName('xarbb');

    $comments = xarModAPIFunc('comments',
                              'user',
                              'get_multiple',
                               array('modid'       => $modid,
                                     'objectid'    => $tid));

     $totalcomments=count($comments);
     $isanon=$comments[$totalcomments-1]['xar_postanon'];
     $anonuid = xarConfigGetVar('Site.User.AnonymousUID');

   // Start by updating the topic stats.
    if ($isanon==1) {
        $poster=$anonuid;
    } else {
        $poster = xarUserGetVar('uid');
    }
    //Don't count up if the topic is being edited ? Need to add modify test here.
    if ($modify != 1){
         if (!xarModAPIFunc('xarbb',
                       'user',
                       'updatetopicsview',
                       array('tid'      => $tid,
                             'treplier' => $poster))) return;
    }
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
                                 'fposter'  => $poster))) return;
    }

   xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));

    return;
}

?>
