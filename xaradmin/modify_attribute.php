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
 * Modify an item of the attributes object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function eav_admin_modify_attribute()
    {
        if (!xarSecurity::check('EditEAV')) return;

        if (!xarVar::fetch('itemid' ,    'int',    $data['itemid'] , 0 ,          xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('confirm',    'bool',   $data['confirm'], false,       xarVar::NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => 'eav_attributes_def'));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSec::confirmAuthKey()) return;

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTpl::module('eav','admin','modify_attribute', $data);        
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarController::redirect(xarController::URL('eav','admin','view_attributes'));
                return true;
            }
        }
        return $data;
    }
?>