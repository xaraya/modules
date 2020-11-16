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

function messages_user_markunread()
{
    if (!xarSecurity::check('ManageMessages')) {
        return;
    }

    if (!xarVar::fetch('id', 'int:1', $id, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(array('name' => 'messages_messages'));
    $data['object']->getItem(array('itemid' => $id));

    $folder = xarSession::getVar('messages_currentfolder');

    // Check the folder, and that the current user is either author or recipient
    switch ($folder) {
        case 'inbox':
            if ($data['object']->properties['to']->value != xarSession::getVar('role_id')) {
                return xarTpl::module('messages', 'user', 'message_errors', array('layout' => 'bad_id'));
            } else {
                $data['object']->properties['recipient_status']->setValue(MESSAGES_STATUS_UNREAD);
            }
            break;
        case 'sent':
            if ($data['object']->properties['from']->value != xarSession::getVar('role_id')) {
                return xarTpl::module('messages', 'user', 'message_errors', array('layout' => 'bad_id'));
            } else {
                $data['object']->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
            }
            break;
    }

    $data['folder'] = $folder;

    $data['object']->updateItem();

    xarResponse::redirect(xarController::URL('messages', 'user', 'view', array('folder' => $folder)));
         
    return true;
}
