<?php
/**
 * XML sitemap
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * XML sitemap
 */
function content_user_sitemap() {

	if (!xarSecurityCheck('ViewContent',1)) return;

	$ctypes = xarMod::apiFunc('content','admin','getcontenttypes');
	$exclude = explode(',',xarModVars::get('content','sitemap_exclude'));

	if (!empty($exclude)) {
		foreach ($exclude as $key => $value) {
			unset($ctypes[$value]);
		}
	}

	sys::import('modules.dynamicdata.class.objects.master');

	$items = array();
 
	foreach ($ctypes as $name => $value) {

		$object = DataObjectMaster::getObject(array(
						'name' => $name
						)); 
 	  
		$properties = array_keys($object->getProperties());

		$filters = array();
		$where = '';
		$join = '';

		if (in_array('publication_date', $properties)) {
			$where .= 'publication_date lt ' . time();
			$join = ' and ';
		}
		//expiration_date can be empty
		if (in_array('expiration_date', $properties)) {
			$where .=  $join . 'expiration_date gt ' . time();
			$join = ' and ';
		}
		if (in_array('publication_status', $properties)) {
			$where .= $join . 'publication_status gt 1';
		}
		if (isset($where)) $filters['where'] = $where;

		$list = DataObjectMaster::getObjectList(array(
						'name' => $name
						)); 

		$items = $list->getItems($filters); 

		foreach ($items as $key => $item) { 
			$item['content_type'] = $name; 
			$all_items[$key.'-'.$name] = $item;
		} 
		
	} 
	 
	$output = '';
	
	if (!empty($all_items)) {
		ksort($all_items);

		foreach ($all_items as $item) { 
			$output .= xarTplModule('content','user','summary', $item, 'sitemap');
		}
	}

	try {
		xarTplSetPageTemplateName('xmlsitemap');
	} catch (Exception $e) {
		try {
			xarTplSetPageTemplateName('xml');
		} catch (Exception $e) {
			xarTplSetPageTemplateName('default');
		}
	}

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('XML Sitemap')));

	$data['output'] = $output;

    return xarTplModule('content','user','view', $data, 'sitemap');
}

?>
