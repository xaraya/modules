<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Create a new item of the path object
 */
function path_admin_add()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddPath')) return;

	if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

	// Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'path'));
	$data['object'] = $object;

	$data['label'] = 'Path';

	if ($data['confirm']) {

		// Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form
        $isvalid = $object->checkInput();

		if (!$isvalid) {
			return xarTplModule('path','admin','add', $data);
		}

		$path = $object->properties['path']->getValue();
		$action = $object->properties['action']->getValue();

		$pathinfo = xarMod::apiFunc('path','user','setpath', array('path' => $path, 'action' => $action));

		if (isset($pathinfo['errors'])) {
			$data['errors'] = $pathinfo['errors'];
			return xarTplModule('path','admin','add', $data);
		}

		xarResponse::redirect(xarModURL('path','admin','view'));
		return true;

	} else {
		return $data;
	}

}

?>