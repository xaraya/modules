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
function messages_user_send()
{

    // Security check
    if (!xarSecurityCheck( 'AddMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to send messages.');
    }

    xarVarFetch('preview', 'checkbox', $preview, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('draft',   'checkbox', $draft,   false, XARVAR_NOT_REQUIRED);
    xarVarFetch('postanon',   'checkbox', $postanon,   false, XARVAR_NOT_REQUIRED);
	xarVarFetch('action',   'str', $action,   false, XARVAR_NOT_REQUIRED);
	xarVarFetch('postanon_to',   'int', $postanon_to,   false, XARVAR_NOT_REQUIRED);	
    //Psspl:Added the code for folder type.
	xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox');
	
    $data['folder'] = (isset($folder))?$folder:'inbox';
	
    if ($preview === true) {
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

    $data['post_url']       = xarModURL('messages','user','send');
    $data['action']         = $action;
    $data['draft']          = $draft;
    $data['postanon']       = $postanon;   
    
    //Psspl:Added the code for configuring the user-menu
	$data['allow_newpm']    = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
        
    if($action != 'submit') {
		$data['users'] = xarModAPIFunc('messages','user','get_users');
		//Psspl:Added the code for checking user list
		if(empty($data['users'])) {
			$msg = xarML('There are no active users for sending messages');
        	//throw new Exception($msg);		
		}
	}
	
	//Psspl:Added the code for read/unread messages. 
	if(isset($action) && $action == 'reply') {
		
		if (!xarVarFetch('id', 'int:1', $id)) return;
		
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
	}	
	 //Psspl:Modifided the code for resolving the issue 
     //of blank messages body or subject sending.
     // djb - moving the numbers to the user-menu, adding these vars 
     $data['unread']                  = xarModAPIFunc('messages','user','count_unread');
     $data['sent']                    = xarModAPIFunc('messages','user','count_sent');
     $data['total']                   = xarModAPIFunc('messages','user','count_total');
     $data['drafts']                  = xarModAPIFunc('messages','user','count_drafts');

    switch($action) {
        case "submit":
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
			if(isset($data['no_subject']) || isset($data['no_body']) || isset($data['no_recipient'])){

                xarTplSetPageTitle( xarML('Post Message') );

                $users = xarModAPIFunc('roles',
                                       'user',
                                       'getall',
                                        array('state'             => 3,
                                              'include_anonymous' => false,
                                              'include_myself'    => false));
                                              
                $data['users']                      = $users;
                $data['input_title']                = xarML('Compose Message');
                $data['action']                     = 'post';
    
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
            
            	$data['id'] 			= $id;
            	$data['post_url']       = xarModURL('messages', 'user', 'send');
            	$data['input_title']    = xarML('Reply to a Message');
            	$data['recipient']      = $replymessages[0]['sender_id'];
            	$data['replymessage']   = $replymessages[0];
			
				// Get $recipient information
        		$recipient_info         = xarRoles::get($data['recipient']);
        		if (!$recipient_info) return;
	       		$data['recipient_name'] = $recipient_info->getName();

            	$data['message']['postanon']  		= $postanon;
            	$data['message']['postanon_to']  	= $replymessages[0]['postanon'];
            	$data['postanon_to']  		        = $replymessages[0]['postanon'];
            	$data['postanon']      				= $postanon;     
				$data['action']                     = 'prev_reply';
				
			}
                return $data;
            }

            $id = xarModAPIFunc('messages',
                          'user',
                          'create',
                           array('subject'     => $subject,
                                 'body'        => $body,
                                 'postanon'    => $postanon,
                                 'postanon_to' => $postanon_to,
                                 'recipient'   => $recipient,
                                 'draft' => $draft));
            // see if the recipient has set an away message
            if(!$draft){
                $isaway = xarModUserVars::get('messages','away_message',$recipient);
                if (!empty($isaway)) {
                    $data['recipient'] = $recipient;
                    $data['away_message'] = $isaway;
                    return xarTplModule('messages','user','away',$data);
                }
            }
            xarResponseRedirect(xarModURL('messages','user','display'));
            return true;
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
            
            $data['id'] 			= $id;
            $data['post_url']       = xarModURL('messages', 'user', 'send');
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
			
			if($subject == null) {
				$data['no_subject'] = 1;
			}
			if($body == null) {
				$data['no_body'] = 1;
			}
			if($recipient == null){
				 $data['no_recipient'] = 1;
			}

			//field for postanom of previous 
            $data['message']['postanon']  		= $postanon;
            $data['message']['postanon_to']  	= $replymessages[0]['postanon'];
            $data['postanon_to']  		        = $replymessages[0]['postanon'];
            $data['postanon']      				= $postanon;     
            
            $data['message']['sender']          = xarUserGetVar('name');
            $data['message']['senderid']        = xarSession::getVar('role_id');
            $data['message']['recipient']       = xarUserGetVar('name',$recipient);
            $data['message']['recipient_id']    = $recipient;
            $data['message']['subject']         = $subject;
            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['message']['raw_date']        = time();
            $data['message']['body']            = $body;
			$data['message']['user_link']       = xarModURL('roles','user','display',
                                                  array('id' => xarUserGetVar('id')));
            
            $data['recipient']                  = $recipient;
            $data['subject']                    = $subject;
            $data['body']                       = $body;
			     	
            break;				
        case "reply":
            if (!xarVarFetch('id', 'int:1', $id)) return;
            xarTplSetPageTitle( xarML('Messages :: Reply') );

            $messages = xarModAPIFunc('messages', 'user', 'get', array('id' => $id));

            if (!count($messages) || !is_array($messages)) {
                $data['error'] = xarML('Message ID nonexistant!');
                return $data;
            }

            if ($messages[0]['recipient_id'] != xarSession::getVar('role_id') &&
                $messages[0]['sender_id'] != xarSession::getVar('role_id')) {
                    $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
                    return $data;
            }
            $data['id'] 			= $id;
            $data['post_url']       = xarModURL('messages', 'user', 'send');
            $data['input_title']    = xarML('Reply to a Message');
            $data['recipient']      = $messages[0]['sender_id'];
            $data['message']        = $messages[0];
			$data['replymessage']   = $data['message'];
		    $data['postanon_to']    = $messages[0]['postanon'];
			$data['postanon']       = 1;
			
			$data['body']     = xarModAPIFunc('messages', 'user', 'reply_message_text', array('message' => $data['message']));
			$subject  = xarModAPIFunc('messages', 'user', 'reply_message_subject', array('message' => $data['message']));
			TracePrint($subject,"Replysubject");
			$data['subject']	= $subject;
			// Get $recipient information
        	$recipient_info = xarRoles::get($data['recipient']);
        	if (!$recipient_info) return;
	       	$data['recipient_name'] = $recipient_info->getName();
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
        case "post":
            $data['postanon'] = 1;
            xarTplSetPageTitle( xarML('Post Message') );

            $data['input_title']    = xarML('Compose Message');
            break;
    }
    return $data;
}

?>