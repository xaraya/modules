<?php

/**
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 */

function messages_user_delete() {

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
