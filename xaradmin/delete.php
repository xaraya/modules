<?php
/**
 * Delete an item
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function foo_admin_delete()
    {
        if (!xarSecurityCheck('ManageFoo')) return;

        if (!xarVarFetch('name',       'str:1',  $name,    '',     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,     'int',    $data['itemid'] , '' ,          XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'foo';
        $data['authid'] = xarSecGenAuthKey('foo');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSecConfirmAuthKey()) return;

            // Delete the item
            $item = $data['object']->deleteItem();
                
            // Jump to the next page
            xarResponse::Redirect(xarModURL('foo','admin','view'));
            return true;
        }
        return $data;
    }

?>