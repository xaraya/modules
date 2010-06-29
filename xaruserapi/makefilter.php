<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage filters
 * @link http://www.xaraya.com/index.php/release/1039.html
 * @author potion <potion@xaraya.com>
 */
/**
 *    Make a filter
* @param required $thisfield name of the property you're filtering on
* @param required $module module for return URL
* @param	optional $type type for return URL
* @param optional $func func for return URL
* @param optional $extra extra params for return URL
* @param optional $startval starting value to display in inputs
* @param optional $clearicon icon for clearing the form
* @param optional $filterlabel label for in front of the input
* @param optional $filterparam URL param
* @param optional $filterfieldparam URL param
* @param optional $width override the CSS filter width (px or %)
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