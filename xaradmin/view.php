<?php
function vendors_admin_view($args)
{
    if (!xarSecurityCheck('EditVendors')) return;
    if (!xarVarFetch('objectname', 'str:1:', $objectname, 'members_members')) return;

    $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => $objectname));
    return $data;
}

?>