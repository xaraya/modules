<?php
/**
 * Delete a a release
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete a releaase
 *
 * @param $rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_delete_release($args)
{
    if (!xarSecurity::check('ManageRelease')) {
        return;
    }

    if (!xarVar::fetch('name', 'str:1', $name, 'release_notes', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'release';
    $data['authid'] = xarSec::genAuthKey('release');

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Delete the item
        $item = $data['object']->deleteItem();

        // Jump to the next page
        xarController::redirect(xarController::URL('release', 'admin', 'view_releases'));
        return true;
    }
    return $data;
}
