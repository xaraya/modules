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

    if (!xarVarFetch('id', 'int:1:', $id)) return;

    //Psspl:Added the code for folder type.
	xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox');
	
    $data['folder'] = (isset($folder))?$folder:'inbox';
    
    //Psspl:Added the code for configuring the user-menu
	$data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
        
    $messages = xarModAPIFunc('messages','user','get',array('id' => $id));

    if (!count($messages) || !is_array($messages)) {
        $data['error'] = xarML('Message ID nonexistant!');
        return $data;
    }

    if ($messages[0]['recipient_id'] != xarSession::getVar('role_id') &&
        $messages[0]['sender_id'] != xarSession::getVar('role_id')) {
            $data['error'] = xarML("You are NOT authorized to view someone else's mail!");
            return $data;
    }

    $read_messages = xarModUserVars::get('messages','read_messages');


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
         $id,
         array($data['message']['body']));

    /*
     * Add this message id to the list of 'seen' messages
     * if it's not already in there :)
     */
    if (!in_array($data['message']['id'], $read_messages)) {
        array_push($read_messages, $data['message']['id']);
        xarModUserVars::set('messages','read_messages',serialize($read_messages));
    }

	// djb - fillin in the status bar / actions 
    $data['unread']                  = xarModAPIFunc('messages','user','count_unread');
    $data['sent']                    = xarModAPIFunc('messages','user','count_sent');
    $data['total']                   = xarModAPIFunc('messages','user','count_total');
    $data['drafts']                  = xarModAPIFunc('messages','user','count_drafts');
    
    return $data;
}

?>
