<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_userapi_getall( $args )
{
	extract($args);

	if(!isset($folder) || !in_array($folder, array('inbox','sent','drafts'))) {
		$folder = 'inbox';
	}

	switch($folder){
	
		case 'inbox':
		    $list = xarModAPIFunc('comments',
		                           'user',
		                           'get_multiple',
		                            array('modid'       => xarModGetIDFromName('messages'),
		                                  'objectid'    => xarUserGetVar('uid'),
		                                  'status'      => 2));
			break;
		case 'sent':
		    $list = xarModAPIFunc('comments',
		                           'user',
		                           'get_multiple',
		                            array('modid'       => xarModGetIDFromName('messages'),
		                                  'author'      => xarUserGetVar('uid'),
		                                  'status'      => 2));
			break;
		case 'drafts':
		    $list = xarModAPIFunc('comments',
		                           'user',
		                           'get_multiple',
		                            array('modid'       => xarModGetIDFromName('messages'),
		                                  'author'      => xarUserGetVar('uid'),
		                                  'status'      => 1));
			break;
	}

    $read_messages = xarModGetUserVar('messages','read_messages');
    if (!empty($read_messages) && $folder == 'inbox') {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['mid']           = $node['xar_cid'];
        $message['sender']        = $node['xar_author'];
        $message['sender_id']     = $node['xar_uid'];
        $message['posting_host']  = $node['xar_hostname'];
        $message['subject']       = $node['xar_title'];
        $message['raw_date']      = $node['xar_datetime'];
        $message['date']          = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['xar_datetime']);
        $message['body']          = $node['xar_text'];
        $message['recipient']    = xarUserGetVar('name', $node['xar_objectid']);
        $message['recipient_id'] = $node['xar_objectid'];

		if($folder == 'inbox'){
	        if (!in_array($message['mid'], $read_messages)) {
	            $message['status_image'] = xarTplGetImage('unread.gif');
	            $message['status_alt']   = xarML('unread');
	        } else {
	            $message['status_image'] = xarTplGetImage('read.gif');
	            $message['status_alt']   = xarML('read');
	        }
		}
		elseif ($folder == 'drafts') {
            $message['status_image'] = xarTplGetImage('draft.gif');
            $message['status_alt']   = xarML('draft');
		}
		else {
            $message['status_image'] = xarTplGetImage('sent.gif');
            $message['status_alt']   = xarML('sent');
		}
		if($folder == 'inbox'){
	        $message['user_link']     = xarModURL('roles','user','display',
	                                               array('uid' => $node['xar_uid']));
	        $message['view_link']     = xarModURL('messages','user', 'view',
	                                               array('mid'    => $node['xar_cid']));
		}
		else {
	        $message['user_link']     = xarModURL('roles','user','display',
	                                               array('uid' => $message['recipient_id']));
	        $message['view_link']     = xarModURL('messages','user', 'viewsent',
    	                                           array('mid'    => $node['xar_cid']));
		}
        $message['reply_link']    = xarModURL('messages','user','send',
                                               array('action' => 'reply',
                                                     'mid'    => $node['xar_cid']));
        $message['modify_link']   = xarModURL('messages','user','modify',
                                               array('mid'    => $node['xar_cid']));
        $message['delete_link']   = xarModURL('messages','user','delete',
                                               array('mid'    => $node['xar_cid'],
                                                     'action' => 'check'));

        $messages[$node['xar_cid']] = $message;
    }


    return $messages;
}

?>