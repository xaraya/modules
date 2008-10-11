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
/**
 *
 * @author Scot Gardner
 */
function messages_newmessagesblock_init()
{
     return true;
}

function messages_newmessagesblock_info()
{
    // Values
    return array('text_type' => 'Messages',
                 'module' => 'messages',
                 'text_type_long' => 'My Messages',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => false);
}


function messages_newmessagesblock_display($blockinfo)
{
       // Security check
    if(!xarSecurityCheck('ReadMessagesBlock', 0)) return;

     // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    $data = array();
    $itemtype=1;

    // Get Logged in Users ID
    $role_id = xarSession::getVar('role_id');

    // Count total Messages
    $totalin = xarModAPIFunc('messages',
                              'user',
                              'get_count',
                              array(
                                  'recipient' => $role_id
                ));
    $data['totalin'] = $totalin;

    // Count Unread Messages
    $unread = xarModAPIFunc('messages',
                              'user',
                              'get_count',
                              array(
                                  'recipient' => xarUserGetVar('id'),
                                  'unread'=>true
                ));
    $data['unread'] = $unread;

    // No messages return emptymessage
    if (empty($unread) || $unread == 0){
        $data['emptymessage'] = xarML('No Unread messages in mailbox');
        $data['content'] = 'No new Messages';
        if (empty($data['title'])){
            $data['title'] = xarML('My Messages');
        }

        $blockinfo['content'] = $data;
        return $blockinfo;
    } else {
        $data['emptymessage'] = '';
        $data['numitems'] = $numitems;
        $blockinfo['content'] = $data;

        if (empty($blockinfo['title'])){
            $blockinfo['title'] = xarML('My Messages');
        }
    }
    return $blockinfo;
}

?>
