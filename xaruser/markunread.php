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

function messages_user_markunread()
{
    if (!xarSecurityCheck('AddMessages')) return;

    if (!xarVarFetch('id', 'int:1', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('folder', 'enum:inbox:sent:drafts', $folder, 'inbox', XARVAR_NOT_REQUIRED)) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => 'messages_messages'));
    $data['object']->getItem(array('itemid' => $id));

    $folder = xarSession::getVar('messages_currentfolder');

    // Check the folder, and that the current user is either author or recipient
    switch ($folder) {
        case 'inbox':
            if ($data['object']->properties['to']->value != xarSession::getVar('role_id')) {
                return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
            } else {
                $data['object']->properties['recipient_status']->setValue(MESSAGES_STATUS_UNREAD);
            }
            break;
        case 'sent':
            if ($data['object']->properties['from']->value != xarSession::getVar('role_id')) {
                return xarTplModule('messages','user','message_errors',array('layout' => 'bad_id'));
            } else {
                $data['object']->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
            }
            break;
    }

    $data['folder'] = $folder;

    $data['object']->updateItem();

    xarController::redirect(xarModURL('messages','user','view',array('folder' => $folder)));

    return true;

}

?>
