
<?php

function subitems_userapi_hook_item_delete($args)
{
    extract($args);
    // extrainfo -> module,itemtype,itemid

    // a object should be linked to this hook
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$extrainfo)) return $extrainfo;
    $objectid = $ddobjectlink['objectid'];

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid,
                         			'status' => 1));
    if (!isset($object)) return $extrainfo;

    // get existing subitems
    $ids = xarModAPIFunc('subitems','user','dditems_getids',array('objectid' => $objectid,'itemid' => $extrainfo['itemid']));
    if(!isset($ids))
    	return $extrainfo;

    // when itemids == array() => it will return all ids, but we don't want this
    if(count($ids) > 0)	{
	   $items = xarModAPIFunc('dynamicdata',
                   'user',
                   'getitems',
                   array(
                         'modid' => $object->moduleid,
                         'itemtype' => $object->itemtype,
                         'itemids' => $ids
                         ));
    }
    else
    	$items = Array();

    foreach($items as $ddid => $item)	{
    	if(!xarModAPIFunc('dynamicdata','admin','delete',array(
        				'modid' => $object->moduleid,
                         'itemtype' => $object->itemtype,
                         'itemid' => $ddid
                         ))) return $extrainfo;

        // detach ids -> write db
        if(!xarModAPIFunc('subitems','admin','dditem_detach',array(
            'ddid' => $ddid,
            'objectid' => $objectid
            ))) return $extrainfo;
    }

 /* print "<pre>";
    print_r($object);
    print_r($items);
    print_r($ids);
    die("");   */

	return $extrainfo;
}

?>