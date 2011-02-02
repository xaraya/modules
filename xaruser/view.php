<?php
/**
 * View a list of items
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
 * view content items
 */
function content_user_view($args) {
	
	extract($args);

    if(!xarVarFetch('startnum', 'isset', $startnum, NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('numitems', 'int',   $numitems,  NULL, XARVAR_NOT_REQUIRED)) {return;}
	if(!xarVarFetch('ctype', 'str',   $ctype,  xarModVars::get('content', 'default_ctype'), XARVAR_NOT_REQUIRED)) {return;} 
	if(!xarVarFetch('page_template', 'str', $page_template, NULL, XARVAR_NOT_REQUIRED)) {return;}

	sys::import('modules.dynamicdata.class.objects.master');

	$instance = 'All:'.$ctype.':All';
	if (!xarSecurityCheck('ViewContent',1,'Item',$instance)) return;

	$data['ctype'] = $ctype;
	$data['type'] = 'admin'; //For content_type tabs

	sys::import('modules.dynamicdata.class.objects.master');
    //sys::import('modules.dynamicdata.class.properties.master');

	$output = '';
	if (!isset($template)) 	$template = $ctype;
	if (!isset($summarytemplate)) $summarytemplate = $template;

	$info = DataObjectMaster::getObjectInfo(array('name' => $ctype));
	if(empty($info)) {
		$data['total'] = 0;
		return $data;
	}

	$object = DataObjectMaster::getObject(array('name' => $ctype));
	$config = $object->configuration;

	if (empty($numitems)) {
		if (!empty($config['numitems'])) {
			$numitems = $config['numitems'];
		} else {
			$numitems = xarModVars::get('content', 'items_per_page');
		}
	}

	$properties = array_keys($object->getProperties());

	if(!isset($sort)) {
		$sort = xarMod::apiFunc('content','admin','sort', array(
			//how to sort if the URL or config say otherwise...
			'object' => $object,
			'sortfield_fallback' => 'itemid', 
			'ascdesc_fallback' => 'ASC'
		));
	}
	$data['sort'] = $sort;

	$filters = array();

	// publication_date and publication_status properties are programmatically added to every content type created by the module, but can be safely removed by admins

	if (!isset($where)) {
		$where = '';
		$join = '';
	} elseif (!empty($where)) {
		$where = $where . ' and ';
	} else {
		$where = '';
	}

	if (in_array('publication_date', $properties)) {
		$where .= 'publication_date lt ' . time();
		$join = ' and ';
	}
	//expiration_date can be empty
	if (in_array('expiration_date', $properties)) {
		$where .=  $join . 'expiration_date gt ' . time() . ' or expiration_date eq -1';
		$join = ' and ';
	}
	if (in_array('publication_status', $properties)) {
		$where .= $join . 'publication_status gt 1';
	}
	if (isset($where)) $filters['where'] = $where;
	 
	$total = DataObjectMaster::getObjectList(array(
						'name' => $ctype,
						'status'    =>DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
						'numitems' => NULL 
						));
	$data['total'] = count($total->getItems($filters));  

	$object = DataObjectMaster::getObjectList(array(
						'name' => $ctype,
						'status'    =>DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
						'startnum'  => $startnum,
						'numitems'  => $numitems,
						 'sort'      => $data['sort']
						));

	$data['object'] = $object;
	$items = $object->getItems($filters); 

	$last = end($items);

	foreach ($items as $item) { 
		
		$itemid = $item['itemid'];
		if (xarModAlias::resolve($ctype) == 'content') {
			$item['link'] = xarModURL('content','user','display', array('itemid'=>$itemid, 'ctype' => $ctype));
		} else { 
			$item['link'] = xarModURL('content','user','display', array('itemid'=>$itemid));
		}

		$item['ctype'] = $data['ctype'];
		$item['lastid'] = $last['itemid']; //useful for styling the last item differently
		$output .= xarTplModule('content','user','summary', $item, $summarytemplate);
	}

	// first see if the $pagetpl is set explicitly for this call
	if (isset($page_template)) { 
		$pagetpl = $page_template;
	} else { 
		// see if this content type has a page template configured
		$config = $object->configuration;
		if (!empty($config['page_template'])) {
			$pagetpl = $config['page_template'];
		} else { 
			$pagetpl = xarModVars::get('content','default_view_page_tpl'); 
		}
	}

	try {
		xarTplSetPageTemplateName($pagetpl);
	} catch (Exception $e) {
		xarTplSetPageTemplateName('default');
	}

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View Content')));

	$data['output'] = $output;

    return xarTplModule('content','user','view', $data, $template);
}

?>
