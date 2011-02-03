<?php
/**
 * View items
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
 * view items
 */
function content_admin_view() {

	if(!xarVarFetch('ctype', 'isset', $ctype, NULL, XARVAR_DONT_SET)) {return;}
	
	$data['showfilters'] = false;

	$data['content_types'] = xarMod::apiFunc('content','admin','getcontenttypes', array('getlabels' => true));
	if (empty($ctype) || !isset($data['content_types'][$ctype])) {
		$ctype = xarModVars::get('content', 'default_ctype');
		// TODO: this returns the first value when a default ctype is deleted?  need to add something to the deletecontenttype function to check if a deleted ctype was the default and if so set a new default
		if (empty($ctype) || !isset($data['content_types'][$ctype])) {
			$ctypes = array_keys($data['content_types']);
			asort($ctypes);
			$ctype = reset($ctypes);
		}
	}   

	$instance = 'All:'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('EditContent',1,'Item',$instance)) {
		return;
	}

	$data['ctype'] = $ctype;
	$data['type'] = 'admin'; //For content_type tabs

	sys::import('modules.dynamicdata.class.objects.master');
	//sys::import('modules.dynamicdata.class.properties.master');

	$object = DataObjectMaster::getObject(array('name' => $ctype));
	$config = $object->configuration;
	$data['label'] = $object->label;

	// Total number of items for this ctype
	$total = DataObjectMaster::getObjectList(array(
							'name' => $ctype,
							));
	$data['total'] = $total->countItems();

	if($data['total'] == 0) return $data;

	$filters = array();

	$filters_min_items = xarModVars::get('content','filters_min_item_count');

	$data['makefilters'] = array();

	if(isset($config['filterfields']) && xarModIsAvailable('filters') && xarModVars::get('content','enable_filters') && $data['total'] >= $filters_min_items) {
		$data['showfilters'] = true;
		$filterfields = $config['filterfields'];
		$get_results = xarMod::apiFunc('filters','user','dd_get_results', array(
							'filterfields' => $filterfields,
							'object' => $ctype
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
			$numitems = xarModVars::get('content', 'items_per_page');
		}
    }

	$sort = xarMod::apiFunc('content','admin','sort', array(
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
							'name' => $ctype,
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
