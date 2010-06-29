<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * view items
 */
function path_admin_view()
{
    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;}	
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('template', 'str',   $data['template'],  'paths', XARVAR_DONT_SET)) {return;}

	if (empty($numitems)) {
		if ($data['template'] == 'paths') {
			$numitems = xarModVars::get('path', 'items_per_page');
		} elseif ($data['template'] == 'sitemap') {
			$numitems = xarModVars::get('path', 'sitemap_items_per_page');
		}
    }

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('EditPath')) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => 'path'));
	$data['label'] = $object->label;

	// Get the fields to display in the admin interface
	$config = $object->configuration;
	if (!empty($config['adminfields'])) {
		$data['adminfields'] = $config['adminfields'];
	} else {
		$data['adminfields'] = array_keys($object->getProperties());
	}

	$total = DataObjectMaster::getObjectList(array(
							'name' => 'path',
							));
	$items = $total->getItems();
	$data['total'] = count($items);

	$filters = array();

	$filters_min_items = xarModVars::get('path','filters_min_item_count');

	if(xarModIsAvailable('filters') && xarModVars::get('path','enable_filters') && $data['total'] >= $filters_min_items) {
		
		$data['showfilters'] = true;

		if(!xarVarFetch('filter', 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
		if(!xarVarFetch('filterfield', 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}

		$data['filter'] = $filter;
		$data['filterfield'] = $filterfield;

		$data['titlestartval'] = 'Path';

		if(isset($filter)) {

			if ($data['filter'] != $data['titlestartval']) {
				$filters['where'] = $filterfield . ' LIKE "%' . $data['filter'] . '%"';
			}

			$results = DataObjectMaster::getObjectList(array(
										'name' => 'path',
										));
			$items = $results->getItems($filters);
			// See bug 6536.  For now, do it this way in case we want to add another filterfield.
			$data['results'] = count($items);
		}

	} else {
		$data['showfilters'] = false;
	}
    
    // Load the DD master property class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.properties.master');

	$data['sort'] = xarMod::apiFunc('path','admin','sort', array(
		//how to sort if the URL doesn't say otherwise...
		'sortfield_fallback' => 'path', 
		'ascdesc_fallback' => 'ASC'
	));

	// Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array(
					'name' =>  'path',
					'numitems' => $numitems,
					'startnum' => $data['startnum'],
                    'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					'sort' => $data['sort']));
    
    // Get the items 
    $items = $mylist->getItems($filters);
    
    // pass along the whole object list to the template
    $data['mylist'] = & $mylist;

    // Return the template variables defined in this function
    return $data;
}

?>
