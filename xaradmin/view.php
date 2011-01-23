<?php
/**
 * View items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * view items
 */
function downloads_admin_view()
{
    if(!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('expandfn', 'bool', $data['expandfn'], 'false', XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('filter', 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('filterfield', 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}

	$data['filter'] = $filter;

	$instance = 'All:All:All';
	if (!xarSecurityCheck('EditDownloads',1)) {
		return;
	}

	sys::import('modules.dynamicdata.class.objects.master');
	sys::import('modules.dynamicdata.class.properties.master');

	$info = DataObjectMaster::getObjectInfo(array('name' => 'downloads'));
	if(empty($info)) {
		$data['total'] = 0;
		return $data;
	}

	// Get the object label for the template
	$object = DataObjectMaster::getObject(array('name' => 'downloads'));
	$data['label'] = $object->label;

	$total = DataObjectMaster::getObjectList(array(
							'name' => 'downloads',
							));
	$items = $total->getItems();
	$data['total'] = count($items);

	if (xarModIsAvailable('filters') && xarModVars::get('downloads','enable_filters') && $data['total'] >= xarModVars::get('downloads','filters_records_min_item_count')) {
		$data['showfilters'] = true;
	} else {
		$data['showfilters'] = false;
	}

	$filters = array();

	$sort = xarMod::apiFunc('downloads','admin','sort', array(
		//how to sort if the URL or config say otherwise...
		'object' => $object,
		'sortfield_fallback' => 'itemid', 
		'ascdesc_fallback' => 'ASC'
	));
	$data['sort'] = $sort;

	if (isset($filterfield) && $filter != $filterfield) {
		$results = DataObjectMaster::getObjectList(array(
								'name' => 'downloads'
								));
		$data['filterfield'] = $filterfield;
		$filters['where'] = $filterfield . ' LIKE "%' . $data['filter'] . '%"';
		$items = $results->getItems($filters);
		// See bug 6536.  For now, do it this way in case we want to add another filterfield.
		$data['results'] = count($items);
	}
	
    if (empty($numitems)) {
        $numitems = xarModVars::get('downloads', 'items_per_page');
    }

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'downloads',
							'status'    =>DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
							'startnum'  => $startnum,
							'numitems'  => $numitems,
							'sort'      => $sort,
							'fieldlist' => 'itemid,title,directory,filename,roleid'
							));

    $data['count'] = $list->countItems();
    $list->getItems($filters);
    
    // pass along the whole object list to the template
    $data['list'] = $list;

    // Return the template variables defined in this function
    return $data;
}

?>
