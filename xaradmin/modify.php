<?php
/**
 * Modify an item of the foo object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function foo_admin_modify()
    {
        if (!xarSecurityCheck('EditFoo')) return;

        if (!xarVarFetch('name',       'str',    $name,            'foo_foo', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'foo';
        $data['authid'] = xarSecGenAuthKey('foo');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSecConfirmAuthKey()) return;
            echo "X";
            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('foo','user','modify', $data);        
            } else {
                // Good data: create the item
                $item = $data['object']->updateItem();
                
                // Jump to the next page
                xarResponseRedirect(xarModURL('foo','admin','view'));
                return true;
            }
        } else {
            $data['object']->getItem(array('itemid' => $data['itemid']));
        }
        return $data;
    }
?>