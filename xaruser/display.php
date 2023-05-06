<?php

sys::import('modules.dynamicdata.class.objects.master');
function xarayatesting_user_display($args)
{
    extract($args);

    if(!xarVar::fetch('name',     'isset', $name,      NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('itemid',   'isset', $itemid,    NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('tplmodule','isset', $tplmodule, NULL, xarVar::DONT_SET)) {return;}

    $object = DataObjectMaster::getObject(array('name' => $name,
                                         'tplmodule' => $tplmodule));
    if (!isset($object)) return;
    $object->getItem(array('itemid'   => $itemid));

    $data['object'] =& $object;
    return $data;
}


?>
