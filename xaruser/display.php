<?php

sys::import('modules.dynamicdata.class.objects.master');
function xarayatesting_user_display($args)
{
    extract($args);

    if (!xarVar::fetch('name', 'isset', $name, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('tplmodule', 'isset', $tplmodule, null, xarVar::DONT_SET)) {
        return;
    }

    $object = DataObjectMaster::getObject(['name' => $name,
                                         'tplmodule' => $tplmodule, ]);
    if (!isset($object)) {
        return;
    }
    $object->getItem(['itemid'   => $itemid]);

    $data['object'] =& $object;
    return $data;
}
