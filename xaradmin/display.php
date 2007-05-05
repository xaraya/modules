<?php
    function members_admin_display()
    {
        if (!xarSecurityCheck('EditMembers')) return;
        if (!xarVarFetch('itemid', 'int:1:', $itemid)) return;
        if (!xarVarFetch('name', 'str:1:', $name)) return;
        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $name));
        $data['object']->getItem(array('itemid' => $itemid,));
        $data['tplmodule'] = 'members';
        $data['name'] = 'name';
        return xarModFunc('members','user','display',$data);
    }
?>
