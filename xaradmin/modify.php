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
 * Modify an item of the wurfl object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function wurfl_admin_modify()
    {
        if (!xarSecurity::check('EditWurfl')) return;

        if (!xarVar::fetch('name',       'str',    $name,            'wurfl_wurfl', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('itemid' ,    'int',    $data['itemid'] , 0 ,          xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('confirm',    'bool',   $data['confirm'], false,       xarVar::NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'wurfl';
        $data['authid'] = xarSec::genAuthKey('wurfl');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSec::confirmAuthKey()) return;

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTpl::module('wurfl','admin','modify', $data);        
            } else {
                // Good data: create the item
                $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
                
                // Jump to the next page
                xarController::redirect(xarController::URL('wurfl','admin','view'));
                return true;
            }
        }
        return $data;
    }
?>