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
function messages_userapi_get( $args )
{

    extract( $args );

    if (!isset($id) || empty($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'message_id', 'userapi', 'get', 'messages');
        throw new Exception($msg);
    }

    if(!isset($status) || !in_array($status, array(1,2,3))){
        $status = 2;
    }

    $list1 = xarModAPIFunc('comments',
                                   'user',
                                   'get_multiple',
                                    array('modid'       => xarModGetIDFromName('messages'),
                                          'objectid'    => xarUserGetVar('id'),
                                          'status'      => 2));
    $list = xarModAPIFunc('messages',
                           'user',
                           'get_one',
                            array('id' => $id));
    $read_messages = xarModUserVars::get('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['id']            = $node['id'];
        $message['sender']        = $node['author'];
        $message['sender_id']     = $node['role_id'];
        $message['recipient']     = xarUserGetVar('name',$node['objectid']);
        $message['recipient_id']  = $node['objectid'];
        $message['posting_host']  = $node['hostname'];
        $message['raw_date']      = $node['datetime'];
        $message['date']          = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['datetime']);
        $message['subject']       = $node['title'];
        $message['postanon']	  = $node['postanon'];	
        $message['postanon_to']	  = $node['postanon_to'];	
        $message['body']          = $node['text'];
        $message['draft']         = ($node['status'] == 1 ? true : false);
        if (!in_array($message['id'], $read_messages)) {
            $message['status_image'] = xarTplGetImage('unread.gif');
            $message['status_alt']   = xarML('unread');
        } else {
            $message['status_image'] = xarTplGetImage('read.gif');
            $message['status_alt']   = xarML('read');
        }
        
        /* insert somehow?
        $message['status_image'] = xarTplGetImage('draft.gif');
        $message['status_alt']   = xarML('draft');
        */
        $message['user_link']     = xarModURL('roles','user','display',
                                               array('id' => $node['role_id']));
        $message['view_link']     = xarModURL('messages','user', 'view',
                                               array('id'    => $node['id']));
        $message['reply_link']    = xarModURL('messages','user','send',
                                               array('action' => 'reply',
                                                     'id'    => $node['id']));
        $message['delete_link']   = xarModURL('messages','user','delete',
                                               array('id'    => $node['id'],
                                                     'action' => 'check'));

        $messages[0] = $message;
    }

    return $messages;
}


?>
