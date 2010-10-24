<?php
/**
 * contains the module information
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
 sys::import('modules.comments.xarincludes.defines');
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 * @return array
 */
function comments_admin_view()
{
    // Security Check
    if(!xarSecurityCheck('AdminComments')) {
        return;
    }

	$sort = xarMod::apiFunc('comments','admin','sort', array(
		//how to sort if the URL or config say otherwise...
		'sortfield_fallback' => 'date', 
		'ascdesc_fallback' => 'DESC'
	));
	$data['sort'] = $sort;

	$object = DataObjectMaster::getObject(array('name' => 'comments'));
	$config = $object->configuration; 
	$adminfields = $config['adminfields'];

	$filters = array();

	// Total number of comments for the pager
	$total = DataObjectMaster::getObjectList(array(
							'name' => 'comments',
							'numitems' => NULL,
							'where' => 'status ne ' . _COM_STATUS_ROOT_NODE
							));
	$data['total'] = $total->countItems();

	$filters_min_items = xarModVars::get('comments','filters_min_item_count');

	$data['makefilters'] = array();

	$data['showfilters'] = false;

	if(xarModIsAvailable('filters') && xarModVars::get('comments','enable_filters') && $data['total'] >= $filters_min_items) {
		
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
												'name' => 'comments',
												));
					$items = $results->getItems($filters);
					$data['results'] = count($items);	
				}	
			}

		}

	} 

	if(isset($filters['where'])) {
		$filters['where'] .=  ' and ';
	} else {
		$filters['where'] = '';
	}

	$filters['where'] .= 'status ne ' . _COM_STATUS_ROOT_NODE;

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'comments',
							'sort' => $sort,
							'fieldlist' => $adminfields
		));

	if (!is_object($list)) return;

	$list->getItems($filters);
	
	$data['list'] = $list;

    return $data;

}
?>