<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
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

	if (!xarSecurityCheck('DeleteComments')) return;

	if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('branch',    'bool',   $data['branch'], false,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('redirect',    'str',   $data['redirect'], '',       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('itemtype',    'str',   $data['itemtype'], NULL,       XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('dtype', 'str:1:', $dtype)) return;

	$data['dtype'] = $dtype;

	sys::import('modules.dynamicdata.class.objects.master');

	switch (strtolower($dtype)) {
		case 'item': // delete just one comment
			if (!xarVarFetch('id', 'int', $id)) return;

			$object = DataObjectMaster::getObject(array(
							'name' => 'comments'
			));
			if (!is_object($object)) return;
			$object->getItem(array('itemid' => $id));
			$values = $object->getFieldValues();
			foreach ($values as $key => $val) {
				$data[$key] = $val;
			}

			$delete_args['id'] = $id;

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

	if ($dtype != 'item') { // multiple items
		
		$list = DataObjectMaster::getObjectList(array(
							'name' => 'comments'
			));
		$data['items'] = $list->getItems($filters); 

		$countlist = DataObjectMaster::getObjectList(array(
							'name' => 'comments'
			));
		if ($dtype == 'all') {
			$filters['where'] = 'status ne 3';
		} else {
			$filters['where'] .= ' and status ne 3';
			$modinfo = xarModGetInfo($modid);
			$data['modname'] = $modinfo['displayname'];
		}
		$countitems = $countlist->getItems($filters); 
		$data['count'] = count($countitems);

		if ($data['confirm'] && is_array($data['items'])) {

			if (!xarSecConfirmAuthKey()) return;

			if (!empty($data['items'])) {
				foreach($data['items'] as $val) {
					$object = DataObjectMaster::getObject(array(
									'name' => 'comments'
					));
					if (!is_object($object)) return;
					$object->deleteItem(array('itemid' => $val['id'])); 
				}
			}

		}

	} else {
		if ($data['confirm']) {
			if (!xarSecConfirmAuthKey()) return;
			if ($data['branch']) {
				xarMod::apiFunc('comments','admin','delete_branch',array('node' => $id)); 
			} else {
				xarMod::apiFunc('comments','admin','delete_node',array('node' => $id, 'pid' =>$values['pid'])); 
			}
		}
	}

    $data['authid'] = xarSecGenAuthKey();

	$data['delete_args'] = $delete_args;

	if ($data['confirm'] && !empty($data['redirect'])) {
		if ($data['redirect'] == 'view') {
			xarResponse::redirect(xarModURL('comments','admin','view'));
		} elseif ($data['redirect'] == 'stats') {
			xarResponse::redirect(xarModURL('comments','admin','stats'));
		} elseif (is_numeric($data['redirect'])) {
			xarResponse::redirect(xarModURL('comments','admin','module_stats', array('modid' => $data['redirect'])));
		}
	}

    return $data;

}
?>
