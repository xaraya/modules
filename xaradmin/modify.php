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
        if (!xarSecurity::check('EditSitemapper')) {
            return;
        }

        if (!xarVar::fetch('name', 'str', $name, 'sitemapper_sitemapper', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'sitemapper';
        $data['authid'] = xarSec::genAuthKey('sitemapper');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTpl::module('sitemapper', 'admin', 'modify', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarResponse::redirect(xarController::URL('sitemapper', 'admin', 'view'));
                return true;
            }
        }
        return $data;
    }
