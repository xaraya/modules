<?php
function members_admin_view($args)
{
    if (!xarSecurityCheck('EditMembers')) return;

    $data['object'] = xarModApiFunc('dynamicdata','user','getobjectlist', array('name' => 'members_members'));
    $data['object']->getItems();
    return $data;
}

?>