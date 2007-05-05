<?php
    function members_admin_modify()
    {
        if (!xarVarFetch('itemid', 'int:1:', $itemid))return;
        if (!xarVarFetch('name', 'str:1:', $name, 'members_members')) return;
        if (!xarVarFetch('returnurl', 'str:1:', $returnurl, xarModURL('members','user','view'))) return;
        if (!xarSecurityCheck('EditMembers')) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $name));
        $data['object']->getItem(array('itemid' => $itemid));
        $data['tplmodule'] = 'members';
        $data['return_url'] =  $returnurl;
        $data['authid'] = xarSecGenAuthKey('dynamicdata');
        return $data;
    }
?>