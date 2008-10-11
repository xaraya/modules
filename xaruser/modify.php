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

sys::import('modules.messages.xarincludes.defines');

function messages_user_modify( $args )
{
    if (!xarSecurityCheck('EditMessages')) return;

    if (!xarVarFetch('action', 'enum:modify:submit:preview', $data['action'], 'modify', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $data['folder'], 'inbox', XARVAR_NOT_REQUIRED)) return;

    xarVarFetch('preview', 'checkbox', $preview, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED);

    $data['object'] = DataObjectMaster::getObject(array('name' => $object));
    $data['object']->getItem(array('itemid' => $id));

    if ($preview === true) {
        $data['action'] = 'preview';
    } elseif ($confirm === true) {
        $data['action'] = 'submit';
    } else {
        $data['action'] = 'modify';
    }
    $data['post_url']       = xarModURL('messages','user','modify');
    $data['input_title']    = xarML('Modify this message');
    xarTplSetPageTitle( xarML('Modify Message') );
    $data['id']             = $id;
        
    // Check that the current user is either sender or receiver
    if (($data['object']->properties['to']->value != xarSession::getVar('role_id')) &&
        ($data['object']->properties['from']->value != xarSession::getVar('role_id'))) {
        return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
    }

    //Psspl:Added the code for configuring the user-menu
//    $data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
        

    //Psspl:Modifided the code for getting user list.
    $data['users'] = xarModAPIFunc('messages','user','get_sendtousers');    

    if ($data['object']->properties['pid']->value) {
        // If this is a reply get the previous message

        xarTplSetPageTitle( xarML('Messages :: Reply') );
        $data['input_title']    = xarML('Modify this reply');

        $data['previousobject'] = DataObjectMaster::getObject(array('name' => $object));
        $data['previousobject']->getItem(array('itemid' => $data['object']->properties['pid']->value));
        $data['object']->properties['postanon']->setValue(0);
        $data['object']->properties['pid']->value = $data['previousobject']->properties['id']->value;;
        $data['object']->properties['to']->value = $data['previousobject']->properties['from']->value;
    }

    switch($data['action']) {
        case "submit":

            $isvalid = $data['object']->checkInput();
            
            if(!$isvalid){

                xarTplSetPageTitle( xarML('Modify Message') );
                $data['action']                     = 'post';

                return xarTplModule('messages','user','new',$data);

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
               
                $data['postanon']       = $postanon;
                $data['action']     = 'modify';
                $data['folder']     = 'drafts';                
                return $data;
            }

            $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
            $checkbox->checkInput('is_draft');
            
            // If this is to be a draft, adjust the state
            if ($checkbox->value) {
                $data['object']->properties['author_status']->setValue(MESSAGES_STATUS_DRAFT);
                $data['object']->properties['recipient_status']->setValue(MESSAGES_STATUS_DRAFT);
            } else {
                $data['object']->properties['author_status']->setValue(MESSAGES_STATUS_READ);
                $data['object']->properties['recipient_status']->setValue(MESSAGES_STATUS_UNREAD);
            }
            $id = $data['object']->updateItem();

/*
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
*/
            xarResponseRedirect(xarModURL('messages','user','view',array('folder' => xarSession::getVar('messages_currentfolder'))));
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
            $data['action']                     = 'modify';
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

/*            // added call to transform text srg 09/22/03
            list($data['message']['body']) = xarModCallHooks('item',
                                                             'transform',
                                                             $id,
                                                             array($data['message']['body']));

*/
            return $data;
            break;
    }
}
?>
