<?php
    function members_user_display()
    {
        if (!xarSecurityCheck('ReadMembers')) return;
        if (!xarVarFetch('itemid', 'int:1:', $itemid)) return;
        if (!xarVarFetch('name', 'str:1:', $name,'members_members')) return;
        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $name));
        $data['object']->getItem(array('itemid' => $itemid,));
        $data['tplmodule'] = 'members';
        return $data;
    }
?>