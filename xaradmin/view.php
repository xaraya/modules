<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * View items
 */
function amazonfps_admin_view()
{
	$data['showfilters'] = false;

	if (!xarSecurityCheck('DeleteAmazonFPS',0)) {
		return;
	}
 	
	$name = 'amazonfps_payments';
	$data['name'] = $name;

	$data['type'] = 'admin'; 

	sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	$object = DataObjectMaster::getObject(array('name' => $name));
	$config = $object->configuration;
	$data['label'] = $object->label;

	// Total number of items for this name
	$total = DataObjectMaster::getObjectList(array(
							'name' => $name,
							));
	$data['total'] = $total->countItems();

	if($data['total'] == 0) return $data;

	$filters = array();

	$filters_min_items = xarModVars::get('amazonfps','filters_min_items');

	$data['makefilters'] = array();

	if(xarModIsAvailable('filters') && xarModVars::get('amazonfps','enable_filters') && $data['total'] >= $filters_min_items) {
		$data['showfilters'] = true;
		$filterfields = $config['filterfields'];
		$get_results = xarMod::apiFunc('filters','user','dd_get_results', array(
							'filterfields' => $filterfields,
							'object' => 'amazonfps_payments'
							)); 
		$data = array_merge($data, $get_results);
		if (isset($data['filters'])) $filters = $data['filters'];
	} 

	if(!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_DONT_SET)) {return;}
	
    if (empty($numitems)) {
		if (!empty($config['numitems'])) {
			$numitems = $config['numitems'];
		} else {
			$numitems = xarModVars::get('amazonfps', 'items_per_page');
		}
    }

	$sort = xarMod::apiFunc('amazonfps','admin','sort', array(
		//how to sort if the URL or config say otherwise...
		'object' => $object,
		'sortfield_fallback' => 'itemid', 
		'ascdesc_fallback' => 'ASC'
	));
	$data['sort'] = $sort;

	// Get the fields to display in the admin interface
	if (!empty($config['adminfields'])) {
		$adminfields = $config['adminfields'];
	} else {
		$adminfields = array_keys($object->getProperties());
	}

	$list = DataObjectMaster::getObjectList(array(
							'name' => $name,
							'status'    =>DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
							'startnum'  => $startnum,
							'numitems'  => $numitems,
							'sort'      => $sort,
							'fieldlist' => $adminfields
							));
	
    $data['count'] = $list->countItems();
	$list->getItems($filters);
    $data['list'] = $list; 

    return $data;
}

?>
