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
 * Create a new item of the wurfl object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function wurfl_admin_new()
    {
        if (!xarSecurityCheck('AddWurfl')) return;

        if (!xarVarFetch('name',       'str',    $name,            'wurfl_wurfl', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'wurfl';
        $data['authid'] = xarSecGenAuthKey('wurfl');

        if ($data['confirm']) {
        
            // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
            if(!xarVarFetch('preview', 'str', $preview,  NULL, XARVAR_DONT_SET)) {return;}

            // Check for a valid confirmation key
            if(!xarSecConfirmAuthKey()) return;
            
            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('wurfl','admin','new', $data);        
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem();
                
                // Jump to the next page
                xarController::redirect(xarModURL('wurfl','admin','view'));
                return true;
            }
        }
        return $data;
    }
?>