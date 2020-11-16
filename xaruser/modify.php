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
    if (!xarSecurityCheck('EditMessages', 0)) {
        return;
    }

    if (!xarVarFetch('send', 'str', $send, '', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('draft', 'str', $draft, '', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('saveandedit', 'str', $saveandedit, '', XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('id', 'id', $id, null, XARVAR_NOT_REQUIRED)) {
        return;
    }

    $send = (!empty($send)) ? true : false;
    $draft = (!empty($draft)) ? true : false;
    $saveandedit = (!empty($saveandedit)) ? true : false;

    xarTplSetPageTitle(xarML('Edit Draft'));
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
    $object = DataObjectMaster::getObject(array('name' => 'messages_messages'));
    $object->getItem(array('itemid' => $id));
    $replyto = $object->properties['replyto']->value;
    $data['replyto'] = $replyto;

    $data['reply'] = ($replyto > 0) ? true : false;
    
    $data['object'] = $object;

    $data['to'] = null;

    if ($data['reply']) {
        $reply = DataObjectMaster::getObject(array('name' => 'messages_messages'));
        $reply->getItem(array('itemid' => $replyto)); // get the message we're replying to
        $data['to'] = $reply->properties['from']->value; // get the user we're replying to
        $data['display'] = $reply;
        xarTplSetPageTitle(xarML('Reply to Message'));
        $data['input_title']    = xarML('Reply to Message');
    }

    $data['label'] = $object->label;

    if ($send || $draft || $saveandedit) {

        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges', 'user', 'errors', array('layout' => 'bad_author'));
        }

        // Get the data from the form
        $isvalid = $object->checkInput();

        if (!$isvalid) {
            return xarTplModule('messages', 'user', 'modify', $data);
        } else {
            // Good data: update the item

            if ($send) {
                $object->properties['time']->setValue(time());
                $object->properties['author_status']->setValue(MESSAGES_STATUS_UNREAD);
            }

            $object->updateItem(array('itemid' => $id));

            if ($saveandedit) {
                xarResponse::redirect(xarModURL('messages', 'user', 'modify', array('id'=>$id)));
                return true;
            } elseif ($draft) {
                xarResponse::redirect(xarModURL('messages', 'user', 'view', array('folder'=> 'drafts')));
                return true;
            } elseif ($send) {
                if (xarModVars::get('messages', 'sendemail')) {
                    $to = $object->properties['to']->value;
                    xarMod::apiFunc('messages', 'user', 'sendmail', array('id' => $id, 'to' => $to));
                }
                xarResponse::redirect(xarModURL('messages', 'user', 'view'));
                return true;
            }
        }
    }

    $data['folder'] = 'drafts';

    return $data;
}
