<?php

/**
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 */

function messages_user_delete() 
{

    // Security check
    if (!xarSecurityCheck( 'DeleteMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to delete messages.');
    }

    if (!xarVarFetch('action', 'enum:confirmed:check', $action)) return;
    if (!xarVarFetch('mid', 'int:1', $mid)) return;


    /*
     * Let's make sure the message exists before we
     * try to delete it - otherwise, all sorts of crazy
     * could happen!
     */
    $messages = xarModAPIFunc('messages', 'user', 'get', array('mid' => $mid));

    if (!count($messages)) {
        $data['error']  = xarML('Message id refers to a nonexistant message!');
        return $data;
    }

    switch($action) {
        case "confirmed":

            /*
             * First, make sure we remove the message id from
             * the read messages list, if this message has been seen.
             */
            $read_messages = xarModGetUserVar('messages','read_messages');
            if (!empty($read_messages)) {
                $read_messages = unserialize($read_messages);
            } else {
                $read_messages = array();
            }


            if ( ($key = array_search($mid, $read_messages)) !== FALSE) {
                unset($read_messages[$key]);
                xarmodSetUserVar('messages', 'read_messages', serialize($read_messages));
            }

            /*
             * Then go ahead and delete the message :)
             */
            xarModAPIFunc('messages',
                          'user',
                          'delete',
                           array('mid' => $mid));
            xarResponseRedirect(xarModURL('messages','user','display'));
            break;

        case "check":
            $data['message']    = $messages[0];
            $data['mid']        = $mid;
            $data['action']     = $action;
            $data['post_url']   = xarModURL('messages','user','delete');
            break;
    }

    return $data;
}

?>
