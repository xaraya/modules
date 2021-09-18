<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

function cacher_admin_delete()
{
    if (!xarSecurity::check('ManageCacher')) {
        return;
    }

    if (!xarVar::fetch('name', 'str:1', $name, 'cacher_cacher', xarVar::NOT_REQUIRED)) {
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

    $data['tplmodule'] = 'cacher';
    $data['authid'] = xarSec::genAuthKey('cacher');

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        // Delete the item
        $item = $data['object']->deleteItem();

        // Jump to the next page
        xarController::redirect(xarController::URL('cacher', 'admin', 'view'));
        return true;
    }
    return $data;
}
