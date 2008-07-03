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
function messages_user_modify( $args )
{

    // Security check
    if (!xarSecurityCheck('ViewMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to view messages.');
    }

    if (!xarVarFetch('id', 'int:1:', $id)) return;
    xarVarFetch('preview', 'checkbox', $preview, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('draft', 'checkbox', $draft, true, XARVAR_NOT_REQUIRED);

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
    $data['id']             = $id;

    xarTplSetPageTitle( xarML('Modify Message') );

    $users = xarModAPIFunc('roles',
                           'user',
                           'getall',
                            array('state'   => 3,
                                  'include_anonymous' => false,
                                  'include_myself' => false));
    $data['users']          = $users;

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

                // added call to transform text srg 09/22/03
                list($data['message']['body']) = xarModCallHooks('item',
                     'transform',
                     $id,
                     array($data['message']['body']));
                return $data;
            }

            $id = xarModAPIFunc('messages',
                          'user',
                          'update',
                           array('id' => $id,
                                 'subject' => $subject,
                                 'body'  => $body,
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

        case 'preview';
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
            // added call to transform text srg 09/22/03
            list($body) = xarModCallHooks('item',
                                          'transform',
                                           $id,
                                           array($body));

            xarTplSetPageTitle( xarML('Modify Message') );

            $data['input_title']                = xarML('Compose Message');
            $data['action']                     = 'preview';

            $data['message'] = $messages[0];

            $data['message']['sender']          = xarUserGetVar('name');
            $data['message']['senderid']        = xaruserGetVar('id');
            $data['message']['recipient']       = xarUserGetVar('name',$recipient);
            $data['message']['recipient_id']    = $recipient;
            $data['message']['subject']         = $subject;
            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['message']['raw_date']        = time();
            $data['message']['body']            = $body;

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
            $data['post_url']       = xarModURL('messages','user','modify');
            $data['action']         = $action;
            $data['draft']          = $messages[0]['draft'];
            $data['recipient']      = $messages[0]['recipient_id'];
            $data['subject']        = $messages[0]['subject'];
            $data['date']           = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', time());
            $data['raw_date']       = time();
            $data['body']           = $messages[0]['body'];

            $data['message'] = $messages[0];

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
