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
 * Create a new item of the realms object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function realms_admin_new()
    {
        if (!xarSecurityCheck('AddRealms')) {
            return;
        }

        if (!xarVarFetch('parentid', 'id', $data['parentid'], (int)xarModVars::get('roles', 'defaultgroup'), XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('itemtype', 'int', $data['itemtype'], xarRoles::ROLES_USERTYPE, XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('name', 'str', $name, 'realms_realms', XARVAR_NOT_REQUIRED)) {
            return;
        }
        if (!xarVarFetch('confirm', 'bool', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'realms';
        $data['authid'] = xarSecGenAuthKey('realms');

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
                return xarTplModule('realms', 'admin', 'new', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem(array('name' => $data['object']->properties['name']->getValue()));
                
                // Jump to the next page
                xarController::redirect(xarModURL('realms', 'admin', 'view'));
                return true;
            }
        }
        return $data;
    }
