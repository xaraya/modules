<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */

sys::import('modules.messages.xarincludes.defines');

function messages_user_display($args) 
{

    extract($args);

    if (!xarSecurityCheck('ReadMessages')) return;

    //if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $data['folder'], 'inbox', XARVAR_NOT_REQUIRED)) return;

    $data['id'] = $id;

    xarTplSetPageTitle(xarML('Read Message'));
    $data['input_title']    = xarML('Read Message');

    //Psspl:Added the code for configuring the user-menu
    //$data['allow_newpm'] = xarMod::apiFunc('messages' , 'user' , 'isset_grouplist');

    $object = DataObjectMaster::getObject(array('name' => 'messages_messages'));
    $object->getItem(array('itemid' => $id));

    $data['replyto'] = $object->properties['replyto']->value;

    $current_user = xarSession::getVar('role_id');

    // Check that the current user is either author or recipient
    if (($object->properties['to']->value != $current_user) &&
        ($object->properties['from']->value != $current_user)) {
        return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
    }

//    $data['message'] = $messages[0];
    $data['action']  = 'display';

    // added call to transform text srg 09/22/03
    $body = $object->properties['body']->getValue();
    list($body) = xarModCallHooks('item',
         'transform',
         $id,
         array($body));
     $object->properties['body']->setValue($body);

    /*
     * Mark this message as read
     * Handle author and recipient for 'mark unread' (future)
     */
    if ($current_user == $object->properties['from']->value) {
        // don't update drafts
        if($object->properties['author_status']->value != MESSAGES_STATUS_DRAFT) {
            $object->properties['author_status']->setValue(MESSAGES_STATUS_READ);
            $object->updateItem();
        }
    }
    if ($current_user == $object->properties['to']->value) {
        $object->properties['recipient_status']->setValue(MESSAGES_STATUS_READ);
        $object->updateItem();
    }

    $data['object'] = $object;

    return $data;
}

?>
