<?php
    function vendors_user_filter()
    {
        if (!xarSecurityCheck('ViewVendors')) return;
        if (!xarVarFetch('objectname', 'str:1:', $objectname, 'vendors_vendors')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $objectname));
        $data['properties'] = $data['object']->getProperties();
        $data['action_search'] = xarModURL('vendors','user','validate', array('objectname' => $objectname));

        return $data;
    }
?>