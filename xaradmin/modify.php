<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * modify an item
 *
 * This function shows a form in which the user can modify the item
 *
 * @param id itemid The id of the dynamic data item to modify
 */
function comments_admin_modify()
{
    if (!xarVar::fetch('id', 'id', $id, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('parent_url', 'str', $parent_url, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('view', 'str', $data['view'], '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Check if we still have no id of the item to modify.
    if (empty($id)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item id',
            'admin',
            'modify',
            'comments'
        );
        throw new Exception($msg);
    }

    $data['id'] = $id;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object name
    $commentsobject = DataObjectMaster::getObject(['name' => 'comments']);
    $check = $commentsobject->getItem(['itemid' => $id]);
    if (empty($check)) {
        $msg = 'There is no comment with an itemid of ' . $id;
        return xarTpl::module('base', 'message', 'notfound', ['msg' => $msg]);
    }

    if (!xarSecurity::check('EditComments', 0)) {
        return;
    }

    $data['pathval'] = '';

    // Get the object we'll be working with
    $object = DataObjectMaster::getObject(['name' => 'comments_comments']);
    $data['object'] = $object; // save for later

    $data['label'] = $object->label;

    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    if ($data['confirm']) {
        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
        }

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            return xarTpl::module('comments', 'admin', 'modify', $data);
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTpl::module('comments', 'admin', 'modify', $data);
        } else {
            // Good data: update the item

            $data['object']->updateItem(['itemid' => $id]);

            $values = $data['object']->getFieldValues();

            if (!empty($data['view'])) {
                xarController::redirect($values['parent_url']);
            } else {
                xarController::redirect(xarController::URL('comments', 'admin', 'modify', ['id'=>$id]));
            }
            return true;
        }
    } else {
        $data['object']->getItem(['itemid' => $id]);
    }

    return $data;
}
