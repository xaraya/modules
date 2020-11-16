<?php

sys::import('modules.dynamicdata.class.objects.master');

function eav_admin_display_entity($args)
{
    if (!xarVar::fetch('id', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    
    $data['object'] = DataObjectMaster::getObject(array('name' => 'eav_entities'));
    if (!isset($data['object'])) {
        return;
    }
    //if (!$data['object']->checkAccess('display'))
    //return xarResponse::Forbidden(xarML('Display #(1) is forbidden', $data['object']->label));

    $data['object']->getItem(array('itemid' => $data['itemid']));
    
    return $data;
}
