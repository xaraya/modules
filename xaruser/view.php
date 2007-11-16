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
function messages_user_view( $args )
{

    // Security check
    if (!xarSecurityCheck('ViewMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to view messages.');
    }

    if (!xarVarFetch('mid', 'int:1:', $mid)) return;

    $messages = xarModAPIFunc('messages','user','get',array('mid' => $mid));

    if (!count($messages) || !is_array($messages)) {
        $data['error'] = xarML('Message ID nonexistant!');
        return $data;
    }

    if ($messages[0]['recipient_id'] != xarUserGetVar('uid') &&
        $messages[0]['sender_id'] != xaruserGetVar('uid')) {
            $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
            return $data;
    }

    $read_messages = xarModGetUserVar('messages','read_messages');


    if (!empty($read_messages)) {
        $read_messages = unserialize($read_messages);
    } else {
        $read_messages = array();
    }


    /*
     * if it's not already an array, then this must be
     * the first time we've looked at it
     * so let's make it an array :)
     */
    if (!is_array($read_messages)) {
        $read_messages = array();
    }

    $data['message'] = $messages[0];
    $data['action']  = 'view';

    // added call to transform text srg 09/22/03
    list($data['message']['body']) = xarModCallHooks('item',
         'transform',
         $mid,
         array($data['message']['body']));

    /*
     * Add this message id to the list of 'seen' messages
     * if it's not already in there :)
     */
    if (!in_array($data['message']['mid'], $read_messages)) {
        array_push($read_messages, $data['message']['mid']);
        xarmodSetUserVar('messages','read_messages',serialize($read_messages));
    }

    return $data;
}

?>
