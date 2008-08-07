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

    if ($preview === true) {
        $action = 'preview';
    } elseif ($confirm === true) {
        $action = 'submit';
    } else {
        if (!xarVarFetch('action', 'enum:submit:preview:reply:post', $action)) return;
    }

    $data['post_url']       = xarModURL('messages','user','send');
    $data['action']         = $action;
    $data['draft']          = $draft;
    $data['postanon']       = $postanon;   
    if($action != 'submit') {
        $data['users'] = xarModAPIFunc('messages','user','get_users');
        // djb - moving the numbers to the user-menu, adding these vars 
        $data['unread']                  = xarModAPIFunc('messages','user','count_unread');
        $data['sent']                    = xarModAPIFunc('messages','user','count_sent');
        $data['total']                   = xarModAPIFunc('messages','user','count_total');
        $data['drafts']                  = xarModAPIFunc('messages','user','count_drafts');

    }

    switch($action) {
        case "submit":

            if (!xarVarFetch('subject', 'str:1', $subject)) {
                $data['no_subject'] = 1;
                xarErrorHandled();
            }
            if (!xarVarFetch('body', 'str:1', $body)){
                $data['no_body'] = 1;
                xarErrorHandled();
            }
            if (!xarVarFetch('recipient', 'int:1', $recipient)){
                $data['no_recipient'] = 1;
                xarErrorHandled();
            }

            if(isset($data['no_subject']) || isset($data['no_body']) || isset($data['no_recipient'])){

                xarTplSetPageTitle( xarML('Post Message') );

                $users = xarModAPIFunc('roles',
                                       'user',
                                       'getall',
                                        array('state'   => 3,
                                              'include_anonymous' => false,
                                              'include_myself' => false));
                $data['users']          = $users;
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

                return $data;
            }

            $id = xarModAPIFunc('messages',
                          'user',
                          'create',
                           array('subject' => $subject,
                                 'body'  => $body,
                                 'postanon' => $postanon,
                                 'recipient'    => $recipient,
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
            $data['post_url']       = xarModURL('messages', 'user', 'send');
            $data['input_title']    = xarML('Reply to a Message');
            $data['recipient']     = $messages[0]['sender_id'];
            $data['message']        = $messages[0];

            //Psspl:Added the code for recipient name for anonymous messages
            $data['postanon']      = $messages[0]['postanon'];
            
            // Get $recipient information
            $recipient_info = xarRoles::get($data['recipient']);
            if (!$recipient_info) return;
            $data['recipient_name'] = $recipient_info->getName();                                           
            break;
        case "preview":
            //Psspl:Comment the code for resolving preview message issue
            /*             
            if (!xarVarFetch('id', 'int:1', $id)) {
                $data['id'] = 1;
                xarErrorHandled();
            }
            */
            if (!xarVarFetch('subject', 'str:1', $subject)) {
                $data['no_subject'] = 1;
                xarErrorHandled();
            }
            if (!xarVarFetch('body', 'str:1', $body)){
                $data['no_body'] = 1;
                xarErrorHandled();
            }
            if (!xarVarFetch('recipient', 'int:1', $recipient)){
                $data['no_recipient'] = 1;
                xarErrorHandled();
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
            $data['message']['recipient']      = xarUserGetVar('name',$recipient);
            $data['message']['recipient_id']   = $recipient;
            $data['message']['subject']         = $subject;
            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['message']['raw_date']        = time();
            $data['message']['body']            = $body;

            $data['recipient']                 = $recipient;
            $data['subject']                    = $subject;
            $data['body']                       = $body;

            break;
        case "post":
            xarTplSetPageTitle( xarML('Post Message') );

            $data['input_title']    = xarML('Compose Message');
            break;
    }
    return $data;
}

?>
