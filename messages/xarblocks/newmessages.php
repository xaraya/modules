<?php
/**
 * File: $Id$
 *
 * Messages Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage Messages module
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

    // Database information
    xarModDBInfoLoad('messages');
    list($dbconn) = xarDBGetConn();
    $xartable =xarDBGetTables();
    $messagestable = $xartable['messages'];
    $prefix = xarConfigGetVar('prefix');

    $data = array();
    $itemtype=1;

    // Get Logged in Users ID
    $uid = xarUserGetVar('uid');

    // Count total Messages
     $numitems =& xarModAPIFunc(
        'messages'
        ,'user'
        ,'counttotal'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => $itemtype
        ));
     $data['totalin'] = $numitems;
    // Count Unread Messages

   $numitems =& xarModAPIFunc(
        'messages'
        ,'user'
        ,'countunread'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => $itemtype
        ));
    $data['unread'] = $numitems;

    // No messages return emptymessage
    if (empty($numitems)){
        $data['emptymessage'] = xarML('No Unread messages in mailbox');
        $data['content'] = 'No new Messages';
        if (empty($data['title'])){
            $data['title'] = xarML('My Messages');
        }

        $blockinfo['content'] = $data;
        return $blockinfo;
    }

    $uid = xarUserGetVar('uid');
    $messages = array();
      if ($numitems <= 1){
        $query = "SELECT ".$prefix."_msg_id,
                         ".$prefix."_subject,
                         ".$prefix."_from_userid,
                         ".$prefix."_to_userid,
                         ".$prefix."_read_msg
                         FROM $messagestable
                         WHERE ".$prefix."_to_userid = $uid AND ".$prefix."_read_msg = 0
                         ORDER by ".$prefix."_msg_id";
     }
     else {
     $query = "SELECT ".$prefix."_msg_id,
                         ".$prefix."_subject,
                         ".$prefix."_from_userid,
                         ".$prefix."_to_userid,
                          ".$prefix."_read_msg
                         FROM $messagestable
                          WHERE ".$prefix."_to_userid = $uid AND ".$prefix."_read_msg = 0
                         ORDER by ".$prefix."_msg_id";
      }
   $result = $dbconn->Execute($query);
    for (; !$result->EOF; $result->MoveNext()) {
        list($msg_id, $subject, $from_userid, $to_userid, $read_msg) = $result->fields;
        $messages[] = array('msg_id' => $msg_id,
                             'subject' => $subject,
                             'from_userid' => $from_userid,
                             'to_userid' => $to_userid,
                             'read_msg' => $read_msg);
        }

   $data['emptymessage'] = '';
   $data['numitems'] = $numitems;
   $data['messages'] = $messages;
   $blockinfo['content'] = $data ;

    if (empty($blockinfo['title'])){
        $blockinfo['title'] = xarML('My Messages');
    }


    return $blockinfo;
}

?>
