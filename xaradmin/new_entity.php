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
 * Create a new item of the entities object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    function eav_admin_new_entity()
    {
        if (!xarSecurity::check('AddEAV')) return;
        if (!xarVar::fetch('confirm',    'bool',   $data['confirm'], false,     xarVar::NOT_REQUIRED)) return;
        $data['object'] = DataObjectMaster::getObject(array('name' => 'eav_entities'));
        
        if ($data['confirm']) {
    
            // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
            if(!xarVar::fetch('preview', 'str', $preview,  NULL, xarVar::DONT_SET)) {return;}

            // Check for a valid confirmation key
            if(!xarSec::confirmAuthKey()) return;
        
            // Get the data from the form
            $isvalid = $data['object']->checkInput();
        
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTpl::module('eav','admin','new_entity', $data);        
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem();
                // Jump to the next page
                xarController::redirect(xarController::URL('eav','admin','view_entities'));
                return true;
            }
        }
        return $data;
    }
?>