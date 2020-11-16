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
/**
 * Modify an item of the eav object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function eav_admin_modify()
    {
        if (!xarSecurityCheck('EditEAV')) {
            return;
        }

        if (!xarVarFetch('name', 'str', $name, 'eav_attributes', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('itemid', 'int', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('confirm', 'bool', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'eav';
        $data['authid'] = xarSecGenAuthKey('eav');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if (!xarSecConfirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('eav', 'admin', 'modify', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarController::redirect(xarModURL('eav', 'admin', 'view'));
                return true;
            }
        }
        return $data;
    }
