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
        if (!xarSecurity::check('EditEAV')) return;

        if (!xarVar::fetch('name',       'str',    $name,            'eav_attributes', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('itemid' ,    'int',    $data['itemid'] , 0 ,          xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('confirm',    'bool',   $data['confirm'], false,       xarVar::NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'eav';
        $data['authid'] = xarSec::genAuthKey('eav');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSec::confirmAuthKey()) return;

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTpl::module('eav','admin','modify', $data);        
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarController::redirect(xarController::URL('eav','admin','view'));
                return true;
            }
        }
        return $data;
    }
?>