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

function messages_user_modify()
{
    if (!xarSecurity::check('EditMessages', 0)) {
        return;
    }

    if (!xarVar::fetch('send', 'str', $send, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('draft', 'str', $draft, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('saveandedit', 'str', $saveandedit, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('id', 'id', $id, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    $send = (!empty($send)) ? true : false;
    $draft = (!empty($draft)) ? true : false;
    $saveandedit = (!empty($saveandedit)) ? true : false;

    xarTpl::setPageTitle(xarML('Edit Draft'));
    $data['input_title']    = xarML('Edit Draft');

    // Check if we still have no id of the item to modify.
    if (empty($id)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'id',
            'user',
            'modify',
            'messages'
        );
        throw new Exception($msg);
    }

    $data['id'] = $id;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object name
    $object = DataObjectMaster::getObject(['name' => 'messages_messages']);
    $object->getItem(['itemid' => $id]);
    $replyto = $object->properties['replyto']->value;
    $data['replyto'] = $replyto;

    $data['reply'] = ($replyto > 0) ? true : false;

    $data['object'] = $object;

    $data['to_id'] = null;

    if ($data['reply']) {
        $reply = DataObjectMaster::getObject(['name' => 'messages_messages']);
        $reply->getItem(['itemid' => $replyto]); // get the message we're replying to
        $data['to_id'] = $reply->properties['from_id']->value; // get the user we're replying to
        $data['display'] = $reply;
        xarTpl::setPageTitle(xarML('Reply to Message'));
        $data['input_title']    = xarML('Reply to Message');
    }

    $data['label'] = $object->label;

    if ($send || $draft || $saveandedit) {

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
        }

        // Get the data from the form
        $isvalid = $object->checkInput();

        if (!$isvalid) {
            return xarTpl::module('messages', 'user', 'modify', $data);
        } else {
            // Good data: update the item

            if ($send) {
                $object->properties['time']->setValue(time());
                $object->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
            }

            $object->updateItem(['itemid' => $id]);

            if ($saveandedit) {
                xarResponse::redirect(xarController::URL('messages', 'user', 'modify', ['id'=>$id]));
                return true;
            } elseif ($draft) {
                xarResponse::redirect(xarController::URL('messages', 'user', 'view', ['folder'=> 'drafts']));
                return true;
            } elseif ($send) {
                if (xarModVars::get('messages', 'sendemail')) {
                    $to_id = $object->properties['to_id']->value;
                    xarMod::apiFunc('messages', 'user', 'sendmail', ['id' => $id, 'to_id' => $to_id]);
                }
                xarResponse::redirect(xarController::URL('messages', 'user', 'view'));
                return true;
            }
        }
    }

    $data['folder'] = 'drafts';

    return $data;
}
