<?php

sys::import('modules.dynamicdata.class.objects.master');
function xarayatesting_user_display($args)
{
    extract($args);

    if (!xarVarFetch('name', 'isset', $name, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('itemid', 'isset', $itemid, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('tplmodule', 'isset', $tplmodule, null, XARVAR_DONT_SET)) {
        return;
    }

    $object = DataObjectMaster::getObject(array('name' => $name,
                                         'tplmodule' => $tplmodule));
    if (!isset($object)) {
        return;
    }
    $object->getItem(array('itemid'   => $itemid));

    $data['object'] =& $object;
    return $data;
}
