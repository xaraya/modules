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

sys::import('modules.messages.xarincludes.defines');

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

    $list1 = xarModAPIFunc('messages',
                                   'user',
                                   'get_multiple',
                                    array('author'       => xarUserGetVar('id'),
                                          'status'      => 2));
    $list = xarModAPIFunc('messages',
                           'user',
                           'get_one',
                            array('id' => $id));
    $read_list = xarModAPIFunc('messages',
                                    'user',
                                    'get_multiple',
                                    array('recipient' => xarUserGetVar('id'),
                                            'status' => 1
                                    ));
    $read_messages = array();
    foreach ($read_list as $k => $v) {
        $read_messages[] = $v['id'];
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['id']            = $node['id'];
        $message['sender']        = $node['author'];
        $message['sender_id']     = $node['author_id'];
        $message['recipient']     = $node['recipient'];
        $message['recipient_id']  = $node['recipient_id'];
        $message['raw_date']      = $node['datetime'];
        $message['date']          = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['datetime']);
        $message['subject']       = $node['title'];
        $message['postanon']      = $node['postanon'];  
        $message['body']          = $node['text'];
        $message['author_status'] = $node['author_status'];
        $message['recipient_status'] = $node['recipient_status'];
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
                                               array('id' => $node['author_id']));
        $message['view_link']     = xarModURL('messages','user', 'display',
                                               array('id'    => $node['id']));
        $message['reply_link']    = xarModURL('messages','user','new',
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
