<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Modify an item of a mime object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mime_admin_modify()
    {
        if (!xarSecurityCheck('EditMime')) {
            return;
        }

        if (!xarVarFetch('name', 'str', $name, 'mime_types', XARVAR_NOT_REQUIRED)) {
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

        $data['tplmodule'] = 'mime';
        $data['authid'] = xarSecGenAuthKey('mime');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if (!xarSecConfirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('mime', 'admin', 'modify', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarController::redirect(xarModURL('mime', 'admin', 'view'));
                return true;
            }
        }
        return $data;
    }
