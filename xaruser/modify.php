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
include_once("./modules/commonutil.php");
function messages_user_modify( $args )
{

    // Security check
    if (!xarSecurityCheck('ViewMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to view messages.');
    }
	if (!xarVarFetch('id', 'int', $id , NULL , XARVAR_NOT_REQUIRED)){ 
    	$msg = xarML('Invalid #(1)#(2) for #(3) function #(4)() in module #(5)',
                                 'messages' ,'Id', 'user', 'modify', 'messages');
        throw new Exception($msg);
    }
    xarVarFetch('preview', 'checkbox', $preview, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('draft', 'checkbox', $draft, true, XARVAR_NOT_REQUIRED);
    xarVarFetch('postanon',   'checkbox', $postanon,   false, XARVAR_NOT_REQUIRED);
	xarVarFetch('postanon_to',   'int', $postanon_to,   false, XARVAR_NOT_REQUIRED);	
    if ($preview === true) {
        $action = 'preview';
    } elseif ($confirm === true) {
        $action = 'submit';
    } else {
        $action = 'modify';
    }
    $data['post_url']       = xarModURL('messages','user','modify');
    $data['action']         = $action;
    $data['draft']          = $draft;
    $data['input_title']    = xarML('Modify Message');
	$data['postanon']       = $postanon;	    
    $data['id']             = $id;

    //Psspl:Added the code for resolving issue of djb in modify
    // djb - fillin in the status bar / actions 
    $data['unread']                  = xarModAPIFunc('messages','user','count_unread');
    $data['sent']                    = xarModAPIFunc('messages','user','count_sent');
    $data['total']                   = xarModAPIFunc('messages','user','count_total');
    $data['drafts']                  = xarModAPIFunc('messages','user','count_drafts');
		
	//Psspl:Added the code for configuring the user-menu
	$data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
    	
    xarTplSetPageTitle( xarML('Modify Message') );

	//Psspl:Modifided the code for getting user list.
    $data['users'] = xarModAPIFunc('messages','user','get_users');    

    $messages = xarModAPIFunc('messages','user','get',array('id' => $id, 'status' => 1));

    if (!count($messages) || !is_array($messages)) {
        $data['error'] = xarML('Message ID nonexistant!');
        return $data;
    }

    if ($messages[0]['sender_id'] != xarUserGetVar('id')) {
            $data['error'] = xarML("You are NOT authorized to modify someone else's mail!");
            return $data;
    }

    if (!$messages[0]['draft']) {
            $data['error'] = xarML("This message is not a draft!");
            return $data;
    }

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

                //Psspl:Comment code for resolving draft messages error. 
                // added call to transform text srg 09/22/03
                /*list($data['message']['body']) = xarModCallHooks('item',
                     'transform',
                     $id,
                     array($data['message']['body']));*/
               
                $data['postanon_to']    = $postanon_to;
				$data['postanon']       = $postanon;
                $data['action']		= 'modify';
                $data['folder']     = 'drafts';                
                return $data;
            }

            $id = xarModAPIFunc('messages',
                          'user',
                          'update',
                           array('id' => $id,
                                 'subject' => $subject,
                                 'body'  => $body,
                                 'recipient'    => $recipient,
                                 'postanon' => $postanon,
                                 'draft' => $draft));
            // see if the recipient has set an away message
            if(!$draft){
                $isaway = xarModUserVars::get('messages' , 'away_message' , $recipient);
                if (!empty($isaway)) {
                    $data['recipient'] = $recipient;
                    $data['away_message'] = $isaway;
                    return xarTplModule('messages','user','away',$data);
                }
            }
            xarResponseRedirect(xarModURL('messages','user','display'));
            return true;
            break;

        case 'preview';
            //Psspl:modifided code for Error Handling;
			xarVarFetch('subject',   'str:1', $subject,   null, XARVAR_NOT_REQUIRED);
            //minimum length is 7 character it sets &#160; for processing
		    xarVarFetch('body',   'str:7', $body,   null, XARVAR_NOT_REQUIRED); 
			xarVarFetch('recipient',   'int:1', $recipient,   null, XARVAR_NOT_REQUIRED);
			
			if($subject == null) {
                $data['no_subject'] = 1;
                //xarErrorHandled();
            }
			if($body == null) {
                $data['no_body'] = 1;
                //xarErrorHandled();
            }
			if($recipient == null){
                $data['no_recipient'] = 1;
                //xarErrorHandled();
            }
            // added call to transform text srg 09/22/03
            list($body) = xarModCallHooks('item',
                                          'transform',
                                           $id,
                                           array($body));

            xarTplSetPageTitle( xarML('Modify Message') );

            $data['input_title']                = xarML('Compose Message');
            $data['action']        				= 'modify';
			$data['folder']                     = 'drafts';
            $data['message']                    = $messages[0];

            $data['message']['sender']          = xarUserGetVar('name');
            $data['message']['senderid']        = xaruserGetVar('id');
            $data['message']['recipient']       = xarUserGetVar('name',$recipient);
            $data['message']['recipient_id']    = $recipient;
            $data['message']['subject']         = $subject;
            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['message']['raw_date']        = time();
            $data['message']['body']            = $body;
			$data['message']['postanon']        = $postanon;
            $data['recipient']                  = $recipient;
            $data['subject']                    = $subject;
            $data['body']                       = $body;
			$data['postanon_to']				= $postanon_to;
            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['message']['raw_date']        = time();

            // added call to transform text srg 09/22/03
            list($data['message']['body']) = xarModCallHooks('item',
                 'transform',
                 $id,
                 array($data['message']['body']));


            return $data;
            break;

        case 'modify':
        	$data['folder']         = 'drafts';
            $data['post_url']       = xarModURL('messages','user','modify');
            $data['action']         = $action;
            $data['draft']          = $messages[0]['draft'];
            $data['recipient']      = $messages[0]['recipient_id'];
            $data['subject']        = $messages[0]['subject'];
            $data['date']           = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['raw_date']       = time();
            $data['body']           = $messages[0]['body'];
			$data['postanon_to']    = $messages[0]['postanon_to'];
			$data['postanon']       = $messages[0]['postanon'];
            $data['message']        = $messages[0];

            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['message']['raw_date']        = time();

            // added call to transform text srg 09/22/03
            list($data['message']['body']) = xarModCallHooks('item',
											                 'transform',
											                 $id,
											                 array($data['message']['body']));


            return $data;
            break;
    }
}
?>
