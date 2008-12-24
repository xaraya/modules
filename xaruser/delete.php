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
/**
 * Delete a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  public
 */

sys::import('modules.messages.xarincludes.defines');

function messages_user_delete()
{
    if (!xarSecurityCheck('ManageMessages')) return;

    if (!xarVarFetch('action', 'enum:confirmed:check', $data['action'], 'check', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('object', 'str', $object, 'messages_messages', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', XARVAR_NOT_REQUIRED)) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => $object));
    $data['object']->getItem(array('itemid' => $id));


    // Check the folder, and that the current user is either author or recipient
    switch ($folder) {
        case 'inbox':
            if ($data['object']->properties['to']->value != xarSession::getVar('role_id')) {
                return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
            }
            break;
        case 'drafts':
        case 'sent':
            if ($data['object']->properties['from']->value != xarSession::getVar('role_id')) {
                return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
            }
            break;
    }

    $data['folder'] = $folder;

    switch($data['action']) {
        case "confirmed":

            /*
             * Then go ahead and delete the message :)
             */

            if ($folder == 'inbox') {
                $data['object']->properties['recipient_delete']->setValue(MESSAGES_DELETED);
            } elseif ($folder == 'sent') {
                $data['object']->properties['author_delete']->setValue(MESSAGES_DELETED);
            } else {
                $data['object']->properties['recipient_delete']->setValue(MESSAGES_DELETED);
                $data['object']->properties['author_delete']->setValue(MESSAGES_DELETED);
            }

            $data['object']->updateItem();

            xarResponseRedirect(xarModURL('messages','user','view',array('folder' => $folder)));
            break;

        case "check":
            // nothing to do here, just return the object
            $data['id'] = $data['object']->properties['id']->getValue();
            break;
    }
    return $data;
}

?>
