<?php
function products_adminapi_getconfighook($args)
{
	extract($args);
	if (!isset($extrainfo['tabs'])) $extrainfo['tabs'] = array();
	$module = 'products';
	$tabinfo = array(
			'module'  => $module,
			'configarea'  => 'general',
			'configtitle'  => xarML('Products'),
			'configcontent' => xarModFunc($module,'admin','modifyconfig_general'
			)
	);
	$extrainfo['tabs'][] = $tabinfo;
	return $extrainfo;
}
?>