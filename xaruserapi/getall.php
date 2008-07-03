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
    $list = xarModAPIFunc('comments',
                           'user',
                           'get_multiple',
                            array('modid'       => xarModGetIDFromName('messages'),
                                  'objectid'    => xarSession::getVar('role_id')));

    $read_messages = xarModUserVars::get('messages','read_messages');
    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['id']           = $node['id'];
        $message['sender']        = $node['author'];
        $message['sender_id']     = $node['role_id'];
        $message['posting_host']  = $node['hostname'];
        $message['subject']       = $node['title'];
        $message['raw_date']      = $node['datetime'];
        $message['date']          = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['datetime']);
        $message['body']          = $node['text'];
        $message['receipient']    = xarUserGetVar('name');
        $message['reciepient_id'] = xarSession::getVar('role_id');

        if (!in_array($message['id'], $read_messages)) {
            $message['status_image'] = xarTplGetImage('unread.gif');
            $message['status_alt']   = xarML('unread');
        } else {
            $message['status_image'] = xarTplGetImage('read.gif');
            $message['status_alt']   = xarML('read');
        }

        $message['user_link']     = xarModURL('roles','user','display',
                                               array('id' => $node['role_id']));
        $message['view_link']     = xarModURL('messages','user', 'view',
                                               array('id'    => $node['id']));
        $message['reply_link']    = xarModURL('messages','user','send',
                                               array('action' => 'reply',
                                                     'mid'    => $node['id']));
        $message['delete_link']   = xarModURL('messages','user','delete',
                                               array('mid'    => $node['id'],
                                                     'action' => 'check'));

        $messages[$node['id']] = $message;
    }


    return $messages;
}

?>