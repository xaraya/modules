<?php
/**
 * Filters module 
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage filters
 */
/**
 * @param required str $object the object name
 * @param required str $filterfields list of filterfields in this format: propname,label;propname,label;propname,label; etc
 * @return an array of data describing our filters and number of results for this page load
 */
function filters_userapi_dd_get_results($args) {		

	extract($args);

	if(!xarVarFetch('filter', 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('filterfield', 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}

	$data = array();
	$data['filter'] = $filter;
	$data['filterfield'] = $filterfield;

	if (!empty($filterfields)) { 
		
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
											'name' => $object,
											));
				$items = $results->getItems($filters);
				$data['results'] = count($items);	
				$data['filters'] = $filters;
			}	
		}

	}

	return $data;

}
?>