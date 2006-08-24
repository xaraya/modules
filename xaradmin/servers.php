<?php

function xorba_admin_servers($args)
{
    $data = array();

    // Get the itemtype of the xorba_servers object
    $data['objectinfo'] = xarModApiFunc('dynamicdata','user','getobjectinfo',array('name' => 'xorba_servers'));
    return $data;
}
?>