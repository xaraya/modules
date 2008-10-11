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
            $list = xarModAPIFunc('messages',
                                   'user',
                                   'get_multiple',
                                    array('recipient'   => xarUserGetVar('id'),
                                          'delete'      => MESSAGES_ACTIVE,
                                          'orderby'     => 'id DESC',
                                          'startnum'    => $startnum,'inbox'=>true,
                                          'numitems'    => $numitems));
            break;
        case 'sent':
            $list = xarModAPIFunc('messages',
                                   'user',
                                   'get_multiple',
                                    array('author'      => xarUserGetVar('id'),
                                          'delete'      => MESSAGES_ACTIVE,
                                          'orderby'     => 'id DESC',
                                          'startnum'    => $startnum,
                                          'numitems'    => $numitems));
            break;
        case 'drafts':
            $list = xarModAPIFunc('messages',
                                   'user',
                                   'get_multiple',
                                    array('author'      => xarUserGetVar('id'),
                                          'status'      => MESSAGES_STATUS_DRAFT,
                                          'delete'      => MESSAGES_ACTIVE,
                                          'orderby'     => 'id DESC',
                                          'startnum'    => $startnum,
                                          'numitems'    => $numitems));
            break;
    }

    $messages = array();

    foreach ($list as $key => $node) {
        $message['id']               = $node['id'];
        $message['pid']              = $node['pid'];
        $message['left_id']          = $node['left_id'];
        $message['right_id']         = $node['right_id'];
        $message['author']           = xarUserGetVar('name', $node['author']);
        $message['author_id']        = $node['author'];
        $message['subject']          = $node['title'];
        $message['raw_date']         = $node['datetime'];
        $message['date']             = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', $node['datetime']);
        $message['body']             = $node['text'];
        $message['recipient']        = xarUserGetVar('name', $node['recipient']);
        $message['recipient_id']     = $node['recipient'];
        $message['author_status']    = $node['author_status'];
        $message['recipient_status'] = $node['recipient_status'];
        $message['author_delete']    = $node['author_delete'];
        $message['recipient_delete'] = $node['recipient_delete'];
        $message['postanon']         = $node['postanon'];  

        
        if($folder == 'inbox'){
            if ($message['recipient_status'] == MESSAGES_STATUS_UNREAD) {
                $message['status_image'] = xarTplGetImage('unread.gif');
                $message['status_alt']   = xarML('unread');
            } elseif ($message['recipient_status'] == MESSAGES_STATUS_READ) {
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
                                                   array('id' => $message['author_id']));
            $message['view_link']     = xarModURL('messages','user', 'display',
                                                   array('id'    => $message['id']));
        } else {
            $message['user_link']     = xarModURL('roles','user','display',
                                                   array('id' => $message['recipient_id']));
            $message['view_link']     = xarModURL('messages','user', 'viewsent',
                                                   array('id'    => $message['id']));
        }
        /*
        $message['reply_link']    = xarModURL('messages','user','new',
                                               array('action' => 'reply',
                                                     'id'    => $node['id']));
        $message['modify_link']   = xarModURL('messages','user','modify',
                                               array('id'    => $node['id']));
        $message['delete_link']   = xarModURL('messages','user','delete',
                                               array('id'    => $node['id'],
                                                     'action' => 'check'));
        */
        $messages[$message['id']] = $message;
    }


    return $messages;
}

?>
