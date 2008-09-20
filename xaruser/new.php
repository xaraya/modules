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
function messages_user_new()
{
    if (!xarSecurityCheck('AddMessages')) return;

    if (!xarVarFetch('action', 'enum:post:reply:preview', $data['action'],   'post', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:sent:drafts', $data['folder'], 'sent', XARVAR_NOT_REQUIRED)) return;

    xarVarFetch('preview', 'checkbox', $preview, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED);
    if (!xarVarFetch('phase', 'str', $phase, 'display', XARVAR_NOT_REQUIRED)) return;
    $data['object'] = DataObjectMaster::getObject(array('name' => $object));

    /*
    if ($preview) {
        if($action == 'reply' or $action == 'prev_reply'){
            //$action = 'reply';
        } else {
            $action = 'preview';
        }       
    } elseif ($confirm === true) {
        $action = 'submit';
    } else {
        if (!xarVarFetch('action', 'enum:submit:preview:reply:post', $action)) return;
    }
    */

    $data['post_url']       = xarModURL('messages','user','new');
    
    //Psspl:Added the code for configuring the user-menu
    $data['allow_newpm']    = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
        
    /*if($phase != 'submit') {
        $data['users'] = xarModAPIFunc('messages','user','get_sendtousers');
        //Psspl:Added the code for checking user list
        if(empty($data['users'])) {
            $msg = xarML('There are no active users for sending messages');
            //throw new Exception($msg);        
        }
    }
*/    
    
    switch($data['action']) {
        case "post":
            xarTplSetPageTitle( xarML('Post Message') );
            $data['input_title']    = xarML('Compose Message');
        break;
        
        case "reply":
        // If this is a reply get the previous message
            if ($data['action'] == 'reply') {
                xarVarFetch('id',   'int:1', $id,   null, XARVAR_NOT_REQUIRED);

                // Add the message we're replying to the list of those read
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

                $data['id'] = $id;
                xarTplSetPageTitle( xarML('Messages :: Reply') );
                $data['input_title']    = xarML('Compose a Reply');

                $data['previousobject'] = DataObjectMaster::getObject(array('name' => $object));
                $data['previousobject']->getItem(array('itemid' => $id));
                $data['object']->properties['postanon']->setValue(0);
                $data['object']->properties['pid']->value = $data['previousobject']->properties['id']->value;;
                $data['object']->properties['to']->value = $data['previousobject']->properties['from']->value;
            }
        break;
    }
    
    if ($phase == 'display') {
    } elseif ($phase == 'submit') {
        switch($data['action']) {
            case "post":

                $isvalid = $data['object']->checkInput();
                if(!$isvalid){           
                    return xarTplModule('messages','user','new',$data);
                }
    /*
                    $users = xarModAPIFunc('roles',
                                           'user',
                                           'getall',
                                            array('state'             => 3,
                                                  'include_anonymous' => false,
                                                  'include_myself'    => false));

                    $data['users']                      = $users;

                    $data['message']['sender']          = xarUserGetVar('name');
                    $data['message']['senderid']        = xarUserGetVar('id');
                    $data['message']['recipient']       = xarUserGetVar('name',$recipient);
                    $data['message']['recipient_id']    = $recipient;
                    $data['message']['subject']         = $subject;
                    $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
                    $data['message']['raw_date']        = time();
                    $data['message']['body']            = $body;

                    $data['recipient']                  = $recipient;
                    $data['subject']                    = $subject;
                    $data['body']                       = $body;
                    //Psspl:Added the code for preview message.
                    //Psspl:check for sent messages reply
                    xarVarFetch('id',   'int:1', $id,   null, XARVAR_NOT_REQUIRED);
                    if(isset($id) and $id != null){

                    xarTplSetPageTitle( xarML('Messages :: Reply') );

                        $replymessages = xarModAPIFunc('messages', 'user', 'get', array('id' => $id));



                        if (!count($replymessages) || !is_array($replymessages)) {
                            $data['error'] = xarML('Message ID nonexistant!');
                        return $data;
                    }

                    if ($replymessages[0]['recipient_id'] != xarSession::getVar('role_id') &&
                        $replymessages[0]['sender_id'] != xarSession::getVar('role_id')) {
                            $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
                            return $data;
                    }

                    $data['id']             = $id;
                    $data['post_url']       = xarModURL('messages', 'user', 'new');
                    $data['input_title']    = xarML('Reply to a Message');
                    $data['recipient']      = $replymessages[0]['sender_id'];
                    $data['replymessage']   = $replymessages[0];

                    // Get $recipient information
                    $recipient_info         = xarRoles::get($data['recipient']);
                    if (!$recipient_info) return;
                    $data['recipient_name'] = $recipient_info->getName();

                    $data['message']['postanon']        = $postanon;
                    $data['postanon']                   = $postanon;     
                    $data['action']                     = 'prev_reply';

                }
                    return $data;
                }
    */
                break;

            case "prev_reply" : 

                xarVarFetch('id',   'int:1', $id,   null, XARVAR_NOT_REQUIRED); 

                xarTplSetPageTitle( xarML('Messages :: Reply') );

                $replymessages = xarModAPIFunc('messages', 'user', 'get', array('id' => $id));



                if (!count($replymessages) || !is_array($replymessages)) {
                    $data['error'] = xarML('Message ID nonexistant!');
                    return $data;
                }

                if ($replymessages[0]['recipient_id'] != xarSession::getVar('role_id') &&
                    $replymessages[0]['sender_id'] != xarSession::getVar('role_id')) {
                        $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
                        return $data;
                }

                $data['id']             = $id;
                $data['post_url']       = xarModURL('messages', 'user', 'new');
                $data['input_title']    = xarML('Reply to a Message');
                $data['recipient']      = $replymessages[0]['sender_id'];
                $data['replymessage']   = $replymessages[0];

                // Get $recipient information
                $recipient_info         = xarRoles::get($data['recipient']);
                if (!$recipient_info) return;
                $data['recipient_name'] = $recipient_info->getName();

                xarVarFetch('subject',   'str:1', $subject,   null, XARVAR_NOT_REQUIRED);
                //minimum length is 7 character it sets &#160; for processing
                xarVarFetch('body',   'str:7', $body,   null, XARVAR_NOT_REQUIRED); 
                xarVarFetch('recipient',   'int:1', $recipient,   null, XARVAR_NOT_REQUIRED);

                if($subject == null) $data['no_subject'] = 1;
                if($body == null) $data['no_body'] = 1;
                if($recipient == null)$data['no_recipient'] = 1;

                //field for postanom of previous 
                $data['message']['postanon']        = $postanon;
                $data['postanon']                   = $postanon;     

                $data['message']['sender']          = xarUserGetVar('name');
                $data['message']['senderid']        = xarSession::getVar('role_id');
                $data['message']['recipient']       = xarUserGetVar('name',$recipient);
                $data['message']['recipient_id']    = $recipient;
                $data['message']['subject']         = $subject;
                $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
                $data['message']['raw_date']        = time();
                $data['message']['body']            = $body;
                $data['message']['user_link']       = xarModURL('roles','user','view',
                                                      array('id' => xarUserGetVar('id')));

                $data['recipient']                  = $recipient;
                $data['subject']                    = $subject;
                $data['body']                       = $body;

                break;              
            case "reply":
                $isvalid = $data['object']->checkInput();

                if(!$isvalid){      
                    return xarTplModule('messages','user','new',$data);
                }

                break;
            case "preview":
                xarVarFetch('subject',   'str:1', $subject,   null, XARVAR_NOT_REQUIRED);
               //minimum length is 7 character it sets &#160; for processing
                xarVarFetch('body',   'str:7', $body,   null, XARVAR_NOT_REQUIRED); 
                xarVarFetch('recipient',   'int:1', $recipient,   null, XARVAR_NOT_REQUIRED);

                if($subject == null) {
                    $data['no_subject'] = 1;
                }
                if($body == null) {
                    $data['no_body'] = 1;
                }
                if($recipient == null){
                     $data['no_recipient'] = 1;
                }
                //Psspl:Comment the code for resolving preview message issue
                // added call to transform text srg 09/22/03
                /*list($body) = xarModCallHooks('item',
                                              'transform',
                                               $id,
                                               array($body));
                */
                xarTplSetPageTitle( xarML('Post Message') );

                $data['input_title']                = xarML('Compose Message');
                $data['action']                     = 'preview';

                $data['message']['sender']          = xarUserGetVar('name');
                $data['message']['senderid']        = xarSession::getVar('role_id');
                $data['message']['recipient']       = xarUserGetVar('name',$recipient);
                $data['message']['recipient_id']    = $recipient;
                $data['message']['subject']         = $subject;
                $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
                $data['message']['raw_date']        = time();
                $data['message']['body']            = $body;

                $data['recipient']                  = $recipient;
                $data['subject']                    = $subject;
                $data['body']                       = $body;

                break;
        }
        
        // We passed the checks. Adjust and create
        $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
        $checkbox->checkInput('is_draft');

        // If this is to be a draft, adjust the state
        if ($checkbox->value) $data['object']->properties['state']->setValue(2);
        else $data['object']->properties['state']->setValue(3);
        $id = $data['object']->createItem();

        $state = $data['object']->properties['state']->getValue();
        if ($state == '2') $folder = 'drafts';
        elseif ($state == '3') $folder = 'sent';
        else $folder = 'inbox';

        xarResponseRedirect(xarModURL('messages','user','view', array('folder' => $folder)));
        return true;
    }
    return $data;
}

?>