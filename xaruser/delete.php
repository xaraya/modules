<?php
/**
 * Messages Module
 *
 * @package modules
 * @subpackage messages module
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 * @author Marc Lutolf <mfl@netspan.ch>
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
    if (!xarSecurity::check('ManageMessages')) {
        return;
    }

    if (!xarVar::fetch('action', 'enum:confirmed:check', $data['action'], 'check', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('object', 'str', $object, 'messages_messages', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('replyto', 'int', $data['replyto'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('id', 'int:1', $id, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(['name' => $object]);
    $data['object']->getItem(['itemid' => $id]);

    $folder = xarSession::getVar('messages_currentfolder');

    // Check the folder, and that the current user is either author or recipient
    switch ($folder) {
        case 'inbox':
            if ($data['object']->properties['to']->value != xarSession::getVar('role_id')) {
                return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
            }
            break;
        case 'drafts':
            if ($data['object']->properties['from']->value != xarSession::getVar('role_id')) {
                return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
            }
            break;
        case 'sent':
            if ($data['object']->properties['from']->value != xarSession::getVar('role_id')) {
                return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
            }
            break;
    }

    $data['folder'] = $folder;

    switch ($data['action']) {
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

            xarResponse::redirect(xarController::URL('messages', 'user', 'view', ['folder' => $folder]));
            break;

        case "check":
            // nothing to do here, just return the object
            $data['id'] = $data['object']->properties['id']->getValue();
            break;
    }
    return $data;
}
