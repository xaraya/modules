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
function messages_user_display( $args )
{
    if (!xarSecurityCheck('ViewMessages')) return;

    if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $data['folder'], 'inbox', XARVAR_NOT_REQUIRED)) return;
    
    //Psspl:Added the code for configuring the user-menu
    $data['allow_newpm'] = xarModAPIFunc('messages' , 'user' , 'isset_grouplist');
        
    $data['object'] = DataObjectMaster::getObject(array('name' => $object));
    $data['object']->getItem(array('itemid' => $id));

    // Check that the current user is either sender or receiver
    if (($data['object']->properties['to']->value != xarSession::getVar('role_id')) &&
        ($data['object']->properties['from']->value != xarSession::getVar('role_id'))) {
        return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
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

//    $data['message'] = $messages[0];
    $data['action']  = 'display';

    // added call to transform text srg 09/22/03
    $body = $data['object']->properties['body']->getValue();
    list($body) = xarModCallHooks('item',
         'transform',
         $id,
         array($body));
     $data['object']->properties['body']->setValue($body);

    /*
     * Add this message id to the list of 'seen' messages
     * if it's not already in there :)
     */
    if (!in_array($id, $read_messages)) {
        array_push($read_messages, $id);
        xarModUserVars::set('messages','read_messages',serialize($read_messages));
    }

    return $data;
}

?>
