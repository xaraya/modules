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
 * Delete a comment or a group of comments
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_user_delete()
{
    if (!xarSecurity::check('ManageComments')) {
        return;
    }

    if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('deletebranch', 'bool', $deletebranch, false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('id', 'int', $data['id'], null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('parent_url', 'str', $data['parent_url'], '', xarVar::NOT_REQUIRED)) {
        return;
    }

    if (empty($data['id'])) {
        return xarResponse::NotFound();
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'comments_comments']);
    $data['object']->getItem(['itemid' => $data['id']]);
    $values = $data['object']->getFieldValues();
    foreach ($values as $key => $val) {
        $data[$key] = $val;
    }

    if ($data['confirm']) {
        if ($deletebranch) {
            xarMod::apiFunc(
                'comments',
                'admin',
                'delete_branch',
                ['node' => $header['id']]
            );
            xarController::redirect($data['parent_url']);
            return true;
        } else {
            $data['object']->deleteItem(['itemid' => $data['id']]);
            xarController::redirect($data['parent_url']);
            return true;
        }
    }

    $data['package']['delete_url'] = xarController::URL('comments', 'user', 'delete');

    $comments = xarMod::apiFunc('comments', 'user', 'get_one', ['id' => $data['id']]);
    if ($comments[0]['position_atomic']['right'] == $comments[0]['position_atomic']['left'] + 1) {
        $data['package']['haschildren'] = false;
    } else {
        $data['package']['haschildren'] = true;
    }

    return $data;
}
