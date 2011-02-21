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
function content_admin_viewcontenttypes()
{

	if (!xarSecurityCheck('EditContentTypes',1)) {
		return;
	}

    sys::import('modules.dynamicdata.class.objects.master');
	
	$data['sort'] = xarMod::apiFunc('content','admin','sort', array(
		//how to sort if the URL doesn't say otherwise...
		'sortfield_fallback' => 'itemid', 
		'ascdesc_fallback' => 'ASC'
	));

	$object = DataObjectMaster::getObject(array(
						'name' => 'content_types'
					));
	$config = $object->configuration;

    $list = DataObjectMaster::getObjectList(array(
						'name' => 'content_types',
						'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
						'sort' => $data['sort']
						));

	$data['total'] = $list->countItems();

	$filters = array();

	$filters_min_items = xarModVars::get('content','filters_min_ct_count');

	$data['showfilters'] = false;

	$exists = file_exists(sys::code().'/modules/filters/xaruserapi/dd_get_results.php');
	if ($exists && xarModIsAvailable('filters') && xarModVars::get('content','enable_filters') && $data['total'] >= $filters_min_items) {
		$data['showfilters'] = true;
		$filterfields = $config['filterfields'];
		$get_results = xarMod::apiFunc('filters','user','dd_get_results', array(
							'filterfields' => $filterfields,
							'object' => 'content_types'
							)); 
		$data = array_merge($data, $get_results);
		if (isset($data['filters'])) $filters = $data['filters'];	 
	}

    $list->getItems($filters);
    
    $data['list'] = $list;

    return $data;
}

?>
