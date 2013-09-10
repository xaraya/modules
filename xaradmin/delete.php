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
 * Show comments deletion form
 *
 * This form allows one to delete comments for all hooked modules
 */
function comments_admin_delete()
{

    if (!xarSecurityCheck('ManageComments')) return;

    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('deletebranch',    'bool',   $deletebranch, false,       XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('redirect',    'str',   $data['redirect'], '',       XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype',    'str',   $data['itemtype'], NULL,       XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dtype', 'str', $data['dtype'], "",      XARVAR_NOT_REQUIRED)) return;

    if (empty($data['dtype'])) return xarResponse::NotFound();

    sys::import('modules.dynamicdata.class.objects.master');

    switch (strtolower($data['dtype'])) {
        case 'item': // delete just one comment
            if (!xarVarFetch('itemid', 'int', $itemid)) return;

            $object = DataObjectMaster::getObject(array('name' => 'comments_comments'));
            $object->getItem(array('itemid' => $itemid));
            $values = $object->getFieldValues();
            foreach ($values as $key => $val) {
                $data[$key] = $val;
            }

            $delete_args['id'] = $itemid;

            break;
        case 'object': // delete all comments for a content item
            if (!xarVarFetch('itemtype', 'int', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modid','int:1', $modid)) return;
            if (!xarVarFetch('objectid','int:1', $objectid)) return;

            $filters['where'] = 'itemtype eq ' . $itemtype . ' and modid eq ' . $modid . ' and objectid eq ' . $objectid;

            $delete_args['itemtype'] = $itemtype;
            $delete_args['modid'] = $modid;
            $delete_args['objectid'] = $objectid;

            break;
        case 'itemtype': // delete all comments for an itemtype
            if (!xarVarFetch('itemtype', 'int', $itemtype)) return;
            if (!xarVarFetch('modid','int:1',$modid)) return;

            $filters['where'] = 'itemtype eq ' . $itemtype . ' and modid eq ' . $modid;

            $delete_args['itemtype'] = $itemtype;
            $delete_args['modid'] = $modid;

            break;
        case 'module':  // delete all comments for a module
            if (!xarVarFetch('modid','int:1',$modid)) return;

            $filters['where'] = 'modid eq ' . $modid;

            $delete_args['modid'] = $modid;

            break;
        case 'all': // delete all comments
            $filters = array();
            $delete_args = array();
            break;
    }

    if ($data['dtype'] != 'item') { // multiple items

        $list = DataObjectMaster::getObjectList(array(
                            'name' => 'comments'
            ));
        $data['items'] = $list->getItems($filters);

        $countlist = DataObjectMaster::getObjectList(array(
                            'name' => 'comments'
            ));
        if ($data['dtype'] == 'all') {
            $filters['where'] = 'status ne 3';
        } else {
            $filters['where'] .= ' and status ne 3';
            $modinfo = xarMod::getInfo($modid);
            $data['modname'] = $modinfo['displayname'];
        }
        $countitems = $countlist->getItems($filters);
        $data['count'] = count($countitems);

        if ($data['confirm'] && is_array($data['items'])) {

            if (!xarSecConfirmAuthKey()) return;

            if (!empty($data['items'])) {
                foreach($data['items'] as $val) {
                    $object = DataObjectMaster::getObject(array(
                                    'name' => 'comments_comments'
                    ));
                    if (!is_object($object)) return;
                    $object->deleteItem(array('itemid' => $val['id']));
                }
            }

        }

    } else { // $data['dtype'] == 'item'
        if ($data['confirm']) {
            if (!xarSecConfirmAuthKey()) return;
            if ($deletebranch) {
                xarMod::apiFunc('comments','admin','delete_branch',array('node' => $id));
            } else {
                xarMod::apiFunc('comments','admin','delete_node',array('node' => $id, 'parent_id' =>$values['parent_id']));
            }
        } else {
            $comments = xarMod::apiFunc('comments','user','get_one', array('id' => $itemid));

            if ($comments[0]['position_atomic']['right'] == $comments[0]['position_atomic']['left'] + 1) {
                $data['haschildren'] = false;
            } else {
                $data['haschildren'] = true;
            }
        }
    }

    $data['authid'] = xarSecGenAuthKey();

    $data['delete_args'] = $delete_args;

    if ($data['confirm'] && !empty($data['redirect'])) {
        if ($data['redirect'] == 'view') {
            xarController::redirect(xarModURL('comments','admin','view'));
        } elseif ($data['redirect'] == 'stats') {
            xarController::redirect(xarModURL('comments','admin','stats'));
        } elseif (is_numeric($data['redirect'])) {
            xarController::redirect(xarModURL('comments','admin','module_stats', array('modid' => $data['redirect'])));
        }
    }

    return $data;

}
?>
