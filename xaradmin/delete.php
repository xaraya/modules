<?php
/**
 * Delete an item
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function dynamicdata_util_delete_static()
    {
        if (!xarSecurityCheck('ManageFoo')) return;

        if (!xarVarFetch('name',       'str:1',  $name,    '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,     'int',    $data['itemid'] , '' ,          XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));

        $data['tplmodule'] = 'foo';
        $data['authid'] = xarSecGenAuthKey('foo');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSecConfirmAuthKey()) return;

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('foo','admin','delete', $data);        
            } else {
                // Good data: create the item
                $item = $data['object']->updateItem();
                
                // Jump to the next page
                xarResponse::Redirect(xarModURL('foo','admin','view'));
                return true;
            }
        } else {
            $data['object']->getItem(array('itemid' => $data['itemid']));
        }
        return $data;
    }

?>