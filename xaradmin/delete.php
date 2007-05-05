<?php

function members_admin_delete($args)
{
   extract($args);

    if (!xarVarFetch('itemid', 'int:1:', $itemid)) return;
    if (!xarVarFetch('name', 'str:1:', $name,'members_members')) return;

     if (!xarSecurityCheck('DeleteMembers',1)) return;

        $data['object'] = xarModApiFunc('dynamicdata','user','getobject', array('name' => $name));
        $data['object']->getItem(array('itemid' => $itemid,));
        $data['tplmodule'] = 'members';
        $data['authid'] = xarSecGenAuthKey('dynamicdata');
        return $data;
}

?>
