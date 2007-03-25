<?php
    function vendors_admin_modify()
    {
        if (!xarVarFetch('id', 'int:1:', $id)) return;
        if (!xarVarFetch('objectname', 'str:1:', $objectname, 'vendors_vendors')) return;
        if (!xarSecurityCheck('EditVendors')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $objectname));
        $data['object']->getItem(array('itemid' => $id,));
        $data['tplmodule'] = 'vendors';
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>