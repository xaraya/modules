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

	if(!xarVarFetch('startnum', 'int', $startnum, 1, XARVAR_NOT_REQUIRED)) {return;}

	$sort = xarMod::apiFunc('comments','admin','sort', array(
		//how to sort if the URL or config say otherwise...
		'sortfield_fallback' => 'date', 
		'ascdesc_fallback' => 'DESC'
	));
	$data['sort'] = $sort;

	$object = DataObjectMaster::getObject(array('name' => 'comments'));
	$config = $object->configuration; 
	$adminfields = $config['adminfields'];
	$numitems = xarModVars::get('comments','items_per_page');

	$filters = array();

	// Total number of comments for use in the pager
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
		$filterfields = $config['filterfields'];
		$get_results = xarMod::apiFunc('filters','user','dd_get_results', array(
							'filterfields' => $filterfields,
							'object' => 'comments'
							)); 
		$data = array_merge($data, $get_results);
		if (isset($data['filters'])) $filters = $data['filters'];
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
							'startnum' => $startnum,
							'numitems' => $numitems,
							'fieldlist' => $adminfields
		));

	if (!is_object($list)) return;

	$list->getItems($filters);
	
	$data['list'] = $list;

    return $data;

}
?>