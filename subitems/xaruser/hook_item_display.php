
<?php

function subitems_user_hook_item_display($args)
{
    extract($args);
    // extrainfo -> module,itemtype,itemid
    if (!isset($extrainfo['module'])) {
        $extrainfo['module'] = xarModGetName();
    }
    if (empty($extrainfo['itemtype'])) {
        $extrainfo['itemtype'] = 0;
    }
    if (empty($extrainfo['itemid'])) {
        $extrainfo['itemid'] = $objectid;
    }

    $param['module'] = $extrainfo['module'];
    $param['itemtype'] = $extrainfo['itemtype'];
    $param['itemid'] = $objectid;

    // a object should be linked to this hook
    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',$param)) return;
    $objectid = $ddobjectlink['objectid'];

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $objectid,
                         			'status' => 1));
	if (!isset($object)) return;

    // get existing subitems
    $ids = xarModAPIFunc('subitems','user','dditems_getids',array(
    	'objectid' => $objectid,
	    'itemid' => $param['itemid']));
    if(!isset($ids))
    	return;

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

    $template = $object->name;
	if(!empty($ddobjectlink['template']))
    	$template = $ddobjectlink['template'];

    // output
    $data['properties'] = & $object->getProperties();
    $data['values'] = & $items;
    $data['itemid'] = $param['itemid'];
    $data['objectid'] = $objectid;
    $data['object'] = $object;
    $data['ids'] = $ids;
    return xarTplModule('subitems','user','hook_item_display',$data,$template);
}

?>
