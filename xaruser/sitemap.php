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

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'content'));
	$properties = array_keys($object->getProperties());

	$filters = array();
	$where = '';

	if (in_array('publication_date', $properties)) {
		$where .= 'publication_date lt ' . time();
		$join = ' and ';
	}
	//expiration_date can be empty
	if (in_array('expiration_date', $properties)) {
		$where .=  $join . 'expiration_date gt ' . time() . ' or expiration_date eq -1';
		$join = ' and ';
	}
	if (in_array('publication_status', $properties)) {
		$where .= $join . 'publication_status gt 1';
	}
	if (isset($where)) $filters['where'] = $where;
	 
	$list = DataObjectMaster::getObjectList(array(
						'name' => 'content',
						'numitems' => NULL 
						)); 

	$items = $list->getItems($filters); 

	$output = '';

	foreach ($items as $item) { 
		
		$object = DataObjectMaster::getObject(array(
						'name' => 'content'
						)); 
		$object->getItem(array('itemid' => $item['itemid']));
		$item = $object->getFieldValues();
		$output .= xarTplModule('content','user','summary', $item, 'sitemap');
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
