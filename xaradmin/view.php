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
function content_admin_view()
{
	$data['showfilters'] = false;

	$ctinfo = xarMod::apiFunc('content','admin','ctinfo');
	if (!$ctinfo) {
		$data['content_types'] = array();
		return $data;
	}

	$data['content_types'] = $ctinfo['content_types'];
	$ctype = $ctinfo['ctype'];
	$data['ctype'] = $ctype;

	$instance = 'All:'.$ctype.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('EditContent',1,'Item',$instance)) {
		return;
	}

	$data['type'] = 'admin'; //For content_type tabs

	sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

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

	/*if(xarModIsAvailable('filters') && xarModVars::get('content','enable_filters') && $data['total'] >= $filters_min_items) {
		
		$data['showfilters'] = true;

		if(!xarVarFetch('filter', 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
		if(!xarVarFetch('filterfield', 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}

		$data['filter'] = $filter;
		$data['filterfield'] = $filterfield;
		
		if (!empty($config['filterfields'])) {
			$filterfields = $config['filterfields'];
			$filterfields = explode(';',$filterfields);
			foreach ($filterfields as $f) {
				if (strstr($f,',')) {
					$f = explode(',',$f);
					$k = $f[0];
					$v = $f[1];
					$fields[$k] = $v;
				} elseif (!empty($f)) {
					$k = $f;
					$v = ucwords($f);
					$fields[$k] = $v;
				}	
			}
			$filterfields = $fields;
		} else {
			$filterfields = array();
		}

		foreach ($filterfields as $thisfield=>$startval) {

			$data['makefilters'][$thisfield] = $startval;

			// If we're filtering on this field
			if(isset($filter) && $filterfield == $thisfield) { 
				// If the submitted value is different than the starting value...
				if ($data['filter'] != $startval) {
					$filters['where'] = $filterfield . ' LIKE "%' . $data['filter'] . '%"';
		
					$results = DataObjectMaster::getObjectList(array(
												'name' => $ctype,
												));
					$items = $results->getItems($filters);
					$data['results'] = count($items);	
				}	
			}

		}

	} */

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
