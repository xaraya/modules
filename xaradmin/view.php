<?php
function foo_admin_view($args)
{
    if (!xarSecurityCheck('EditFoo')) return;

    $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => 'roles_users'));
    $data['object']->getItems();
    return $data;
}

?>