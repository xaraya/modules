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
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 */

function messages_user_delete()
{
    if (!xarSecurityCheck('ManageMessages')) return;

    if (!xarVarFetch('action', 'enum:confirmed:check', $data['action'], 'check', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', XARVAR_NOT_REQUIRED)) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => $object));
    $data['object']->getItem(array('itemid' => $id));


    // Check that the current user is either sender or receiver
    if (($data['object']->properties['to']->value != xarSession::getVar('role_id')) &&
        ($data['object']->properties['from']->value != xarSession::getVar('role_id'))) {
        return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
    }

/*
    $messages = xarModAPIFunc('messages', 'user', 'get', array('id' => $id));

    //Psspl:Added the code for configuring the user-menu
    $data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
    if (!count($messages)) {
        $data['error']  = xarML('Message id refers to a nonexistant message!');
        return $data;
    }

    if ($messages[0]['recipient_id'] != xarSession::getVar('role_id') &&
        $messages[0]['sender_id'] != xarSession::getVar('role_id')) {
        $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
        return $data;
    }
*/
    switch($data['action']) {
        case "confirmed":

            /*
             * First, make sure we remove the message id from
             * the read messages list, if this message has been seen.
             */
            $read_messages = xarModUserVars::get('messages','read_messages');
            if (!empty($read_messages)) {
                $read_messages = unserialize($read_messages);
            } else {
                $read_messages = array();
            }


            if ( ($key = array_search($id, $read_messages)) !== FALSE) {
                unset($read_messages[$key]);
                xarModUserVars::set('messages', 'read_messages', serialize($read_messages));
            }

            /*
             * Then go ahead and delete the message :)
             */
            xarModAPIFunc('messages',
                          'user',
                          'delete',
                           array('object' => $data['object'] ,
                                 'folder' => $folder));
            xarResponseRedirect(xarModURL('messages','user','view',array('folder' =>    xarSession::getVar('messages_currentfolder'))));
            break;

        case "check":
            $data['id']         = $id;
        /*
            $data['folder']     = $folder;
            $data['message']    = $messages[0];
            $data['action']     = $action;
            $data['post_url']   = xarModURL('messages','user','delete');
         */  
            /*Psspl:Added the code for read messages.
              * Add this message id to the list of 'seen' messages
              * if it's not already in there :)
            */
            $read_messages = xarModUserVars::get('messages','read_messages');
            if (!empty($read_messages)) {
                $read_messages = unserialize($read_messages);
            } else {
                $read_messages = array();
            }
             if (!in_array($id, $read_messages)) {
                array_push($read_messages, $id);
                xarModUserVars::set('messages','read_messages',serialize($read_messages));
            }
            break;
    }
    return $data;
}

?>
