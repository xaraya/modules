
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
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$extrainfo)) return;
    $objectid = $ddobjectlink['objectid'];

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid,
                         			'status' => 1));

    $data['object'] = $object;
    return $data;
}

?>
