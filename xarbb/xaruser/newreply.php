<?php
/**
 * File: $Id$
 * 
 * Add a new topic reply
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
//TODO FInish this function.
function xarbb_user_newreply()
{
    if (!xarVarFetch('tid','int:1:',$tid,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cid','int:1:',$cid,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase','str:1:',$phase,'',XARVAR_NOT_REQUIRED)) return;

    // Let's get the title, and check to see if we are
    if ((!empty($tid)) && (empty($cid))){
        // The user API function is called
        $data = xarModAPIFunc('xarbb',
                              'user',
                              'gettopic',
                              array('tid' => $tid));

        if ($data['tstatus'] == 3) {
            $msg = xarML('Topic -- #(1) -- has been locked by administrator', $data['ttitle']);
            xarExceptionSet(XAR_USER_EXCEPTION, 'LOCKED_TOPIC', new SystemException($msg));
            return;
        }

        $package['title'] = xarVarPrepForDisplay($data['ttitle']);
        if ($phase == 'quote'){
            $package['text'] = '[quote]'. $data['tpost'] .'[/quote]';
        } elseif ($phase == 'edit') {
            $package['text'] = $data['tpost'];
        }
    } elseif (!empty($cid)){
        // The user API function is called
        $data = xarModAPIFunc('comments',
                              'user',
                              'get_one',
                              array('cid' => $cid));

        foreach ($data as $comment){
            $package['title'] = xarVarPrepForDisplay($comment['xar_title']);
            if ($phase == 'quote'){
                $package['text'] = '[quote]'. $comment['xar_text'] .'[/quote]';
            } elseif ($phase == 'edit') {
                $package['text'] = $comment['xar_text'];
            }
        }
        $data = xarModAPIFunc('xarbb',
                      'user',
                      'gettopic',
                      array('tid' => $tid));
    }

    if(!$topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid))) return;

    // Security Check
    if($phase == "edit")    {
	    if(!xarSecurityCheck('ModxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;
	}    else	{
   	    if(!xarSecurityCheck('PostxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;
    }

    // Var Set-up
    $header['input-title']  = xarML('Post a Reply');
    $header['modid']        = xarModGetIDFromName('xarbb');
    $header['objectid']     = $tid;
    $header['cid'] 			= $cid;

	if ($phase == 'edit') {
    	$action = 'modify';
        $receipt['returnurl']['decoded'] = xarModUrl('xarbb', 'user', 'updatetopic', array('tid' => $tid, 'modify' => 1));
    } else {
    	$action = 'reply';
        $receipt['returnurl']['decoded'] = xarModUrl('xarbb', 'user', 'updatetopic', array('tid' => $tid));
    }

    $receipt['post_url']    = xarModUrl('comments', 'user', $action, array('tid' => $tid));
    $receipt['action']      = $action;
    $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);

    $package['name']        = xarUserGetVar('name');
    $package['uid']         = xarUserGetVar('uid');

    //Add images
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" alt="'.xarML('Profile').'" />';

    // Form Hooks
    $formhooks = xarModAPIFunc('xarbb','user','formhooks');
    $data['hooks']      = $formhooks;
    $data['receipt']    = $receipt;
    $data['package']    = $package;
    $data['header']     = $header;
    $data['authid']     = xarSecGenAuthkey();

    return $data;
}


?>
