<?php
function vendors_user_view($args)
{
    $role = xarRoles::get(xarModGetUserVar('members','defaultgroup',xarMod::getRegID('vendors')));
    $data['defaultgroup'] = $role->getName();
    return $data;
}

?>