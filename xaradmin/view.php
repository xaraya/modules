<?php
function vendors_admin_view($args)
{
    if (!xarSecurityCheck('EditVendors')) return;
    if (!xarVarFetch('objectname', 'str:1:', $objectname, 'vendors_vendors')) return;

    $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => $objectname));
    $whereclause = "roles_authmodid eq " . xarMod::getID('vendors') . " and roles_state ne " . ROLES_STATE_DELETED;
    $data['object']->getItems(array('where' => $whereclause));
    return $data;
}

?>