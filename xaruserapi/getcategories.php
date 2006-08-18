<?php

function maps_userapi_getcategories($args)
{
	$cats = xarModAPIFunc('categories','user','getchildren',
			array(
				'cid' => xarModVars::get('maps', 'basecategory'),
				'descendants' => 'list',
			));
	if (isset($args['asoptions']) && $args['asoptions']) {
		$options = array();
		foreach ($cats as $cat) $options[] = array('id' => $cat['cid'], 'name' => $cat['name']);
		return $options;
	} else {
		return $cats;
	}
}

?>
