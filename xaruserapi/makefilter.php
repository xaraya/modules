<?php
/**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage filters
 * @link http://www.xaraya.com/index.php/release/1039.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *    Make a filter
* @param	str		$thisfield	required name of the property you're filtering on
* @param	str		$module	required module for return URL
* @param	str		$type type for return URL
* @param str		$func func for return URL
* @param array	 $extra extra params for return URL
* @param str	 $startval starting value to display in inputs
* @param str	 $clearicon icon for clearing the form
* @param str	 $filterlabel label for in front of the input
* @param str	 $filterparam URL param
* @param str	 $filterfieldparam URL param
* @param str	 $width override the CSS filter width (px or %)
*/
function filters_userapi_makefilter($args) {

	$extra = array();
	$startval = '-filter-';
	$type = 'admin';
	$func = 'view';
	$clearicon = 'none.png';
	$filterparam = 'filter';
	$filterfieldparam = 'filterfield';
	$width = '';

	extract($args);
	
	if(!isset($filterlabel)) $filterlabel = $startval;

	// $filter is the user input
	// $filterfield is the field we're filtering on
	if(!xarVarFetch($filterparam, 'str', $filter, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch($filterfieldparam, 'str', $filterfield, NULL, XARVAR_NOT_REQUIRED)) {return;}

	if(isset($filter) && $filter != $startval && $thisfield == $filterfield) {
		//The filter is active for this page view
		$icon = $clearicon;
		$filterval = $filter;
	} else {
		//The filter is not active for this page view
		$icon = 'blank.png';
		$filterval = $startval;
	}

	$data['filter'] = $filterval;
	$data['filterfield'] = $thisfield; //Because we might have more than one filter
	$data['icon'] = $icon;
	$data['type'] = $type;
	$data['func'] = $func;
	$data['module'] = $module;

	$data['filterlabel'] = $filterlabel;
	$data['startval'] = $startval;
	$data['extra'] = $extra;
	$data['width'] = $width;
		
	return xarTplModule('filters','gui','filter', $data);
}

?>