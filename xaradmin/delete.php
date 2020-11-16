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
        if (!xarSecurityCheck('ManageWurfl')) {
            return;
        }

        if (!xarVarFetch('name', 'str:1', $name, 'wurfl_wurfl', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('itemid', 'int', $data['itemid'], '', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('confirm', 'str:1', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'wurfl';
        $data['authid'] = xarSecGenAuthKey('wurfl');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if (!xarSecConfirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();
                
            // Jump to the next page
            xarController::redirect(xarModURL('wurfl', 'admin', 'view'));
            return true;
        }
        return $data;
    }
