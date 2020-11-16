<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Delete an item
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function realms_admin_delete_member()
    {
        if (!xarSecurityCheck('ManageRealms')) {
            return;
        }

        if (!xarVarFetch('itemid', 'int', $data['itemid'], '', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('confirm', 'bool', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => 'realms_members'));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'realms';

        if ($data['confirm']) {

            // Check for a valid confirmation key
            if (!xarSecConfirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();
                
            // Jump to the next page
            xarController::redirect(xarModURL('realms', 'admin', 'view_members'));
            return true;
        }
        return $data;
    }
