<?php
function messages_user_send() 
{

    // Security check
    if (!xarSecurityCheck( 'AddMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to send messages.');
    }

    if (!xarVarFetch('action', 'enum:submit:preview:reply:post', $action)) return;

    $data['post_url']       = xarModURL('messages','user','send');
    $data['action']         = $action;

    if($action != 'submit') {
        $users = xarModAPIFunc('roles',
                               'user',
                               'getall',
                                array('state'   => 3,
                                      'include_anonymous' => false,
                                      'include_myself' => false));
        $data['users']          = $users;
    }

    switch($action) {
        case "submit":
            if (!xarVarFetch('subject', 'str:1', $subject)) return;
            if (!xarVarFetch('body', 'str:1', $body)) return;
            if (!xarVarFetch('receipient', 'int:1', $receipient)) return;

	    phpinfo();
            xarModAPIFunc('messages',
                          'user',
                          'create',
                           array('subject' => $subject,
                                 'body'  => $body,
                                 'receipient'    => $receipient));
            xarResponseRedirect(xarModURL('messages','user','display'));
            break;

        case "reply":
            if (!xarVarFetch('mid', 'int:1', $mid)) return;
            xarTplSetPageTitle( xarML('Messages :: Reply') );

            $messages = xarModAPIFunc('messages', 'user', 'get', array('mid' => $mid));

            if (!count($messages) || !is_array($messages)) {
                $data['error'] = xarML('Message ID nonexistant!');
                return $data;
            }

            if ($messages[0]['receipient_id'] != xarUserGetVar('uid') &&
                $messages[0]['sender_id'] != xaruserGetVar('uid')) {
                    $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
                    return $data;
            }
            $data['post_url']       = xarModURL('messages', 'user', 'send');
            $data['input_title']    = xarML('Reply to a Message');
            $data['receipient']     = $messages[0]['sender_id'];
            $data['message']        = $messages[0];

            break;
        case "preview":
            if (!xarVarFetch('mid', 'int:1', $mid)) {
                $data['mid'] = 1;
                xarExceptionHandled();
            }
            
            if (!xarVarFetch('subject', 'str:1', $subject)) {
                $data['no_subject'] = 1;
                xarExceptionHandled();
            }
            if (!xarVarFetch('body', 'str:1', $body)){
                $data['no_body'] = 1;
                xarExceptionHandled();
            }
            if (!xarVarFetch('receipient', 'int:1', $receipient)){
                $data['no_receipient'] = 1;
                xarExceptionHandled();
            }
            // added call to transform text srg 09/22/03      
            list($body) = xarModCallHooks('item',
                                          'transform',
                                           $mid,
                                           array($body));

            $data['input_title']                = xarML('Preview your Message');
            $data['action']                     = 'preview';

            $data['message']['sender']          = xarUserGetVar('uname');
            $data['message']['senderid']        = xaruserGetVar('uid');
            $data['message']['receipient']      = xarUserGetVar('uname',$receipient);
            $data['message']['receipient_id']   = $receipient;
            $data['message']['subject']         = $subject;
            $data['message']['date']            = xarLocaleFormatDate('%A, %B %d @ %H:%M:%S', microtime());
            $data['message']['body']            = $body;

            $data['receipient']                 = $receipient;
            $data['subject']                    = $subject;
            $data['body']                       = $body;

            break;
        case "post":
            xarTplSetPageTitle( xarML('Messages :: Post Message') );

            $data['input_title']    = xarML('Compose Message');
            break;
    }
    return $data;
}

?>
