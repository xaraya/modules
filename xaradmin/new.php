<?php
/**
 * Create a new item of the foo object
 *
 */
    function foo_admin_new()
    {
        if (!xarSecurityCheck('AddFoo')) return;

        if (!xarVarFetch('name',       'str',    $name,            'foo_foo', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false, XARVAR_DONT_SET)) return;

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
                return xarTplModule('foo','user','new', $data);        
            } else {
                // Good data: create the item
                $item = $data['object']->createItem();
                
                // Jump to the next page
                xarResponseRedirect(xarModURL('foo','admin','view'));
                return true;
            }
        }
        return $data;
    }
?>