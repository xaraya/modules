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

    // Security check
    if (!xarSecurityCheck( 'DeleteMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to delete messages.');
    }

    if (!xarVarFetch('action', 'enum:confirmed:check', $action)) return;
    if (!xarVarFetch('id', 'int:1', $id)) return;


    /*
     * Let's make sure the message exists before we
     * try to delete it - otherwise, all sorts of crazy
     * could happen!
     */
    $messages = xarModAPIFunc('messages', 'user', 'get', array('id' => $id));

    if (!count($messages)) {
        $data['error']  = xarML('Message id refers to a nonexistant message!');
        return $data;
    }

    if ($messages[0]['recipient_id'] != xarSession::getVar('role_id') &&
        $messages[0]['sender_id'] != xarSession::getVar('role_id')) {
        $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
        return $data;
    }

    switch($action) {
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
                           array('id' => $id));
            xarResponseRedirect(xarModURL('messages','user','display'));
            break;

        case "check":
            $data['message']    = $messages[0];
            $data['id']         = $id;
            $data['action']     = $action;
            $data['post_url']   = xarModURL('messages','user','delete');
            break;
    }

    return $data;
}

?>
