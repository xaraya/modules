<?php

function subitems_admin_ddobjectlink_view($args)	{
	$data = xarModAPIFunc('subitems','admin','menu');

    $items = xarModAPIFunc('subitems','user','ddobjectlink_getall');
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    for($i = 0; $i < count($items); $i++) 	{
        if(xarModIsAvailable($items[$i]['module']))
	    	$items[$i]['modinfo'] = xarModGetInfo(xarModGetIdFromName($items[$i]['module']));
        $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                    array('objectid' => $items[$i]['objectid']));
        if (!empty($objectinfo))
	    	$items[$i]['label'] = $objectinfo['label'];
    }

    $data['ddobjects'] = $items;
    return $data;
}

?>
