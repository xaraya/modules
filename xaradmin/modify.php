<?php
    function vendors_admin_modify()
    {
        if (!xarVarFetch('itemid', 'int:1:', $itemid)) return;
        if (!xarVarFetch('name', 'str:1:', $name, 'vendors_vendors')) return;
        if (!xarVarFetch('returnurl', 'str:1:', $returnurl, xarModURL('vendors','user','view'))) return;
        if (!xarSecurityCheck('EditVendors')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $name));
        $data['object']->getItem(array('itemid' => $itemid,));
        $data['tplmodule'] = 'vendors';
        $data['return_url'] =  $returnurl;
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>