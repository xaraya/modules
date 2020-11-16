<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Modify an item of the sitemapper objects
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function sitemapper_admin_modify()
    {
        if (!xarSecurityCheck('EditSitemapper')) {
            return;
        }

        if (!xarVarFetch('name', 'str', $name, 'sitemapper_sitemapper', XARVAR_NOT_REQUIRED)) {
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

        $data['tplmodule'] = 'sitemapper';
        $data['authid'] = xarSecGenAuthKey('sitemapper');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if (!xarSecConfirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('sitemapper', 'admin', 'modify', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarResponse::redirect(xarModURL('sitemapper', 'admin', 'view'));
                return true;
            }
        }
        return $data;
    }
