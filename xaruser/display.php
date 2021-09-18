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

sys::import('modules.messages.xarincludes.defines');

function messages_user_display($args)
{
    extract($args);

    if (!xarSecurity::check('ReadMessages')) {
        return;
    }

    //if (!xarVar::fetch('object', 'str', $object, 'messages_messages', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('id', 'int', $id, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('folder', 'enum:inbox:sent:drafts', $data['folder'], 'inbox', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['id'] = $id;

    xarTpl::setPageTitle(xarML('Read Message'));
    $data['input_title']    = xarML('Read Message');

    //Psspl:Added the code for configuring the user-menu
    //$data['allow_newpm'] = xarMod::apiFunc('messages' , 'user' , 'isset_grouplist');

    $object = DataObjectMaster::getObject(['name' => 'messages_messages']);
    $object->getItem(['itemid' => $id]);

    $data['replyto'] = $object->properties['replyto']->value;

    $current_user = xarSession::getVar('role_id');

    // Check that the current user is either author or recipient
    if (($object->properties['to']->value != $current_user) &&
        ($object->properties['from']->value != $current_user)) {
        return xarTpl::module('messages', 'user', 'message_errors', ['layout' => 'bad_id']);
    }

//    $data['message'] = $messages[0];
    $data['action']  = 'display';

    // added call to transform text srg 09/22/03
    $body = $object->properties['body']->getValue();
    [$body] = xarModHooks::call(
        'item',
        'transform',
        $id,
        [$body]
    );
    $object->properties['body']->setValue($body);

    /*
     * Mark this message as read
     * Handle author and recipient for 'mark unread' (future)
     */
    if ($current_user == $object->properties['from']->value) {
        // don't update drafts
        if ($object->properties['author_status']->value != MESSAGES_STATUS_DRAFT) {
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
