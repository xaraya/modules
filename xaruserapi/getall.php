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
//Psspl:Modifided the code for post anonymously. 

include_once("./modules/commonutil.php");
function messages_userapi_getall( $args )
{
    extract($args);
	
    if(!isset($folder) || !in_array($folder, array('inbox','sent','drafts'))) {
        $folder = 'inbox';
    }

	$numitems = xarModVars::get('messages', 'itemsperpage');
	$startnum = isset($startnum)?$startnum : 1;
    switch($folder){
    
        case 'inbox':
            $list = xarModAPIFunc('comments',
                                   'user',
                                   'get_multiple',
                                    array('modid'       => xarModGetIDFromName('messages'),
                                          'objectid'    => xarUserGetVar('id'),
                                          'status'      => 2,
                                          'delete_to' => 1,
                                          'orderby'   => 'id DESC',
                                          'startnum' => $startnum,
                                          'numitems' => $numitems));
            break;
        case 'sent':
            $list = xarModAPIFunc('comments',
                                   'user',
                                   'get_multiple',
                                    array('modid'       => xarModGetIDFromName('messages'),
                                          'author'      => xarUserGetVar('id'),
                                          'status'      => 2,
                                          'delete_from' => 1,
                                          'orderby'   => 'id DESC',
                                          'startnum' => $startnum,
                                          'numitems' => $numitems));
            break;
        case 'drafts':
            $list = xarModAPIFunc('comments',
                                   'user',
                                   'get_multiple',
                                    array('modid'       => xarModGetIDFromName('messages'),
                                          'author'      => xarUserGetVar('id'),
                                          'status'      => 1,
                                          'delete_from' => 1,
                                          'orderby'   => 'id DESC',
                                          'startnum' => $startnum,
                                          'numitems' => $numitems));
            break;
    }

    $read_messages = xarModUserVars::get('messages','read_messages');
    if (!empty($read_messages) && $folder == 'inbox') {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['id']            = $node['id'];
        $message['sender']        = $node['author'];
        $message['sender_id']     = $node['role_id'];
        $message['posting_host']  = $node['hostname'];
        $message['subject']       = $node['title'];
        $message['raw_date']      = $node['datetime'];
        $message['date']          = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['datetime']);
        $message['body']          = $node['text'];
        $message['recipient']     = xarUserGetVar('name');
        $message['postanon']	  = $node['postanon'];	
        $message['postanon_to']	  = $node['postanon_to'];	
        $message['recipient_id']  = xarSession::getVar('role_id');

		
        if ($folder == 'sent' || $folder == 'drafts') {

        	$MessageInfo = xarModAPIFunc('messages',
										 'user',
								         'get_one',
								         array('id' => $node['id'] , 'folder' => $folder));
        	
	        foreach ($MessageInfo as $info_key => $Info_node) {
        		
        		$message['recipient']     = xarUserGetVar('name',$Info_node['objectid']);
        		$message['recipient_id']  = $Info_node['objectid'];
        	}
        }
        if($folder == 'inbox'){
            if (!in_array($message['id'], $read_messages)) {
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
                                                   array('id' => $node['role_id']));
            $message['view_link']     = xarModURL('messages','user', 'view',
                                                   array('id'    => $node['id']));
        } else {
            $message['user_link']     = xarModURL('roles','user','display',
                                                   array('id' => $message['recipient_id']));
            $message['view_link']     = xarModURL('messages','user', 'viewsent',
                                                   array('id'    => $node['id']));
        }
        $message['reply_link']    = xarModURL('messages','user','send',
                                               array('action' => 'reply',
                                                     'id'    => $node['id']));
        $message['modify_link']   = xarModURL('messages','user','modify',
                                               array('id'    => $node['id']));
        $message['delete_link']   = xarModURL('messages','user','delete',
                                               array('id'    => $node['id'],
                                                     'action' => 'check'));

        $messages[$node['id']] = $message;
    }


    return $messages;
}

?>