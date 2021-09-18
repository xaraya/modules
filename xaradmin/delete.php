<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Delete an item
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');

    function wurfl_admin_delete()
    {
        if (!xarSecurity::check('ManageWurfl')) {
            return;
        }

        if (!xarVar::fetch('name', 'str:1', $name, 'wurfl_wurfl', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'str:1', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(['name' => $name]);
        $data['object']->getItem(['itemid' => $data['itemid']]);

        $data['tplmodule'] = 'wurfl';
        $data['authid'] = xarSec::genAuthKey('wurfl');

        if ($data['confirm']) {

            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();

            // Jump to the next page
            xarController::redirect(xarController::URL('wurfl', 'admin', 'view'));
            return true;
        }
        return $data;
    }
