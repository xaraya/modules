<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    sys::import('modules.dynamicdata.class.objects.master');
    function eav_admin_delete_attribute()
    {
        if (!xarSecurity::check('ManageEAV')) {
            return;
        }

        if (!xarVar::fetch('name', 'str:1', $name, 'eav_eav', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'str:1', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(['name' => 'eav_attributes_def']);

        // Get that specific item of the object
        $data['object']->getItem(['itemid' => $data['itemid']]);

        $data['tplmodule'] = 'eav';
        $data['authid'] = xarSec::genAuthKey('eav');

        if ($data['confirm']) {
            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();
            // Jump to the next page
            xarController::redirect(xarController::URL('eav', 'admin', 'view_attributes'));
            return true;
        }
        return $data;
    }
