<?php

/**
 * Add a new topic reply
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage  xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 * @author Jo Dalle Nogare
*/

/**
 * @TODO Finish this function.
 */

function xarbb_user_newreply()
{
    if (!xarVarFetch('tid', 'int:1:', $tid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cid', 'int:1:', $cid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:quote:edit', $phase, '', XARVAR_NOT_REQUIRED)) return;

    // Let's get the title, and check to see if we are
    if ((!empty($tid)) && (empty($cid))){
        // The user API function is called

        $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

        if ($data['tstatus'] == 3) {
            $msg = xarML('Topic -- #(1) -- has been locked by administrator', $data['ttitle']);
            xarErrorSet(XAR_USER_EXCEPTION, 'LOCKED_TOPIC', new SystemException($msg));
            return;
        }

        $settings               = unserialize(xarModGetVar('xarbb', 'settings.' . $data['fid']));
        $data['allowhtml']      = $settings['allowhtml'];
        $data['allowbbcode']    = $settings['allowbbcode'];

        $allowhtml= (!empty($data['allowhtml']) ? $data['allowhtml'] : false);
        $allowbbcode= (!empty($data['allowbbcode']) ? $data['allowbbcode'] : false);

        $package['title'] = xarVarPrepForDisplay($data['ttitle']);

        if (($phase == 'quote') && ($allowbbcode == true)){
            $package['text'] = '[quote]' . $data['tpost'] . '[/quote]';
        }elseif (($phase == 'quote') && ($allowhtml == true)){
            $package['text'] = '<blockquote>' . $data['tpost'] . '</blockquote>';
        } elseif ($phase == 'edit') {
            $package['text'] = $data['tpost'];
        }
    } elseif (!empty($cid)){
        $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
        $settings               = unserialize(xarModGetVar('xarbb', 'settings.' . $topic['fid']));
        $data['allowhtml']      = $settings['allowhtml'];
        $data['allowbbcode']    = $settings['allowbbcode'];
        $allowhtml= isset($data['allowhtml']) ? $data['allowhtml'] : false;
        $allowbbcode= isset($data['allowbbcode']) ? $data['allowbbcode'] : false;

        // The user API function is called
        $data = xarModAPIFunc('comments', 'user', 'get_one', array('cid' => $cid));

        foreach ($data as $comment){
            $package['title'] = $comment['xar_title']; //prepped in template
            $package['postanon'] = $comment['xar_postanon'];
            if (($phase == 'quote') && ($allowbbcode == true)){
                $package['text'] = '[quote]' . $comment['xar_text'] . '[/quote]';
            }elseif (($phase == 'quote') && ($allowhtml == true)){
                $package['text'] = '<blockquote>' . $comment['xar_text'] . '</blockquote>';
             } elseif ($phase == 'edit') {
                $package['text'] = $comment['xar_text'];
            }
        }
    }
    $topic = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));
    if (empty($topic)) return;

    // Security Check
    if ($phase == 'edit'){
        if (!xarUserIsLoggedIn()) {
            unset($cid);
            $msg = xarML('You do not have access to modify this topic.');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }

        $uid = xarUserGetVar('uid');
        if (!xarSecurityCheck('ModxarBB', 0, 'Forum', $topic['catid'] . ':' . $topic['fid'])) {
            // No Privs, Hows about this is my comment?
            if ($uid != $data[0]['xar_uid']) {
                // Nope?  Lets return
                $message = xarML('You do not have access to modify this reply');
                return $message;
            }
        }
    } else {
        if (!xarSecurityCheck('PostxarBB', 1, 'Forum', $topic['catid'] . ':' . $topic['fid'])) return;
    }

    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // Var Set-up
    $header['input-title']  = xarML('Post a Reply');
    $header['modid']        = xarModGetIDFromName('xarbb');
    $header['objectid']     = $tid;
    $header['itemtype']     = $data['fid'];
    $header['cid']          = $cid;

    if ($phase == 'edit') {
        $action = 'modify';
        $receipt['returnurl']['decoded'] = xarModURL('xarbb', 'user', 'updatetopic', array('tid' => $tid, 'modify' => 1));
    } else {
        $action = 'reply';
        $receipt['returnurl']['decoded'] = xarModURL('xarbb', 'user', 'updatetopic', array('tid' => $tid));
    }

    $receipt['post_url']    = xarModURL('comments', 'user', $action, array('tid' => $tid));
    $receipt['action']      = $action;
    //$receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);

    $package['name']        = xarUserGetVar('name');
    $package['uid']         = xarUserGetVar('uid');

    //Add images
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" alt="' . xarML('Profile') . '" />';

    // Form Hooks
    $itemtype = $data['fid'];
    $formhooks = xarModAPIFunc('xarbb', 'user', 'formhooks', array('itemtype' => $itemtype));
    $data['hooks']      = $formhooks;
    $data['receipt']    = $receipt;
    $data['package']    = $package;
    $data['header']     = $header;
    $data['authid']     = xarSecGenAuthkey();

    xarTplSetPageTitle(xarML('Reply to #(1)', $data['ttitle']));
    $xarbbtitle         = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $data['xarbbtitle'] = (isset($xarbbtitle) ? $xarbbtitle : '');

    return $data;
}

?>