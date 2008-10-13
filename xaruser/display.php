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

    $current_user = xarSession::getVar('role_id');

    // Check that the current user is either sender or receiver
    if (($data['object']->properties['to']->value != $current_user) &&
        ($data['object']->properties['from']->value != $current_user)) {
        return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
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
     * Mark this message as read
     * Handle author and recipient for 'mark unread' (future)
     */
    if ($current_user == $data['object']->properties['from']->value) {
        // don't update drafts
        if($data['object']->properties['author_status']->value != MESSAGES_STATUS_DRAFT) {
            $data['object']->properties['author_status']->setValue(MESSAGES_STATUS_READ);
            $data['object']->updateItem();
        }
    } 
    if ($current_user == $data['object']->properties['to']->value) {  
        $data['object']->properties['recipient_status']->setValue(MESSAGES_STATUS_READ);
        $data['object']->updateItem();
    }

    return $data;
}

?>
