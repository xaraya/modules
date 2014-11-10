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

    if (!xarSecurityCheck('ManageComments')) return;
    
    if (!xarVarFetch('confirm',      'bool',  $data['confirm'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('deletebranch', 'bool',  $deletebranch, false,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id',           'int',   $data['id'],     NULL,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parent_url',   'str',   $data['parent_url'], '', XARVAR_NOT_REQUIRED)) return;

    if (empty($data['id'])) return xarResponse::NotFound();
    
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));
    $data['object']->getItem(array('itemid' => $data['id']));
    $values = $data['object']->getFieldValues();
    foreach ($values as $key => $val) $data[$key] = $val;

    if ($data['confirm']) {

        if ($deletebranch) {
            xarMod::apiFunc('comments','admin','delete_branch',
                              array('node' => $header['id']));
                xarController::redirect($data['parent_url']);
                return true;
        } else {
            $data['object']->deleteItem(array('itemid' => $data['id']));
            xarController::redirect($data['parent_url']);
            return true;
        }
    }

    $data['package']['delete_url'] = xarModURL('comments','user','delete');

    $comments = xarMod::apiFunc('comments','user','get_one', array('id' => $data['id']));
    if ($comments[0]['position_atomic']['right'] == $comments[0]['position_atomic']['left'] + 1) {
        $data['package']['haschildren'] = false;
    } else {
        $data['package']['haschildren'] = true;
    }

    return $data;
}

?>