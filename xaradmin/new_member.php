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
 * Create a new item of the realms member object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function realms_admin_new_member()
    {
        if (!xarSecurityCheck('AddRealms')) {
            return;
        }

        if (!xarVarFetch('name', 'str', $name, 'realms_members', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('confirm', 'bool', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'realms';

        if ($data['confirm']) {
        
            // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
            if (!xarVarFetch('preview', 'str', $preview, null, XARVAR_DONT_SET)) {
                return;
            }

            // Check for a valid confirmation key
            if (!xarSecConfirmAuthKey()) {
                return;
            }
            
            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('realms', 'admin', 'new_member', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem();
                
                // Jump to the next page
                xarController::redirect(xarModURL('realms', 'admin', 'view_members'));
                return true;
            }
        }
        return $data;
    }
