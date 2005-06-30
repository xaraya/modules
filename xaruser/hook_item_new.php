<?php

function subitems_user_hook_item_new($args)
{
    extract($args);
    // extrainfo -> module,itemtype,itemid
    if (!isset($extrainfo['module'])) {
        $extrainfo['module'] = xarModGetName();
    }
    if (empty($extrainfo['itemtype'])) {
        $extrainfo['itemtype'] = 0;
    }

    // a object should be linked to this hook
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$extrainfo)) return '';
    // nothing to see here
    if (empty($ddobjectlink)) return '';

    $data = array();
    $data['subitems'] = array();
    foreach($ddobjectlink as $index => $subobjectlink) {
        $subobjectid = $subobjectlink['objectid'];

        // get some object information for this subobject (no need for a DD object here)
        $data['subitems'][$subobjectid] = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                                        array('objectid' => $subobjectid));
    }

    return xarTplModule('subitems','user','hook_item_new',$data);
}

?>
